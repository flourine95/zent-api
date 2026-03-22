# Roadmap & Technical Decisions

## Việc cần làm (theo thứ tự ưu tiên)

### 1. Chuyển tất cả ID sang UUID v7

Hiện tại hầu hết bảng dùng `bigint auto-increment` (`$table->id()`). Nên chuyển sang UUID v7.

**Tại sao UUID v7 thay vì v4?**
- v4 hoàn toàn random → index B-tree bị fragmentation nặng khi insert nhiều
- v7 có time-ordered prefix → insert tuần tự, index hiệu quả gần bằng bigint
- Không lộ số lượng records qua ID (bảo mật)
- Phù hợp distributed system sau này

**Laravel support:** `HasUuids` trait từ Laravel 10, UUID v7 từ Laravel 11.

**Chi phí:** Đây là thay đổi breaking — cần migrate toàn bộ foreign keys. Nên làm sớm khi DB còn ít data thật.

---

### 2. Dọn lỗi logic còn tồn đọng

- **Address ownership:** `CreateOrderAction` chưa kiểm tra `address_id` có thuộc về user hiện tại không — user có thể truyền địa chỉ của người khác
- **Shipment ownership:** `ShipmentController::show()` và `cancel()` chưa verify order có thuộc về user không
- **`inventory_reservations.user_id` thừa:** đã có `order_id` → biết user qua order, không cần lưu riêng

---

### 3. Queue cho async tasks

Laravel Queue là framework xử lý background jobs. "Driver" là nơi lưu trữ jobs:

```
Laravel Queue (framework)
    ├── driver: database  → jobs lưu vào bảng `jobs` trong PostgreSQL
    ├── driver: redis     → jobs lưu vào Redis
    └── driver: sync      → chạy luôn, không queue (default local)
```

**Bước 1 — Dùng `database` driver trước:**
Không cần setup thêm infra, đã có PostgreSQL. Chuyển các tác vụ sau sang queued jobs:
- Gửi email (order confirmation, notifications) — hiện đang sync, làm chậm response
- `ReleaseExpiredReservations` — hiện là scheduled command, nên là queued job

**Bước 2 — Chuyển sang Redis driver khi cần:**
Khi queue table PostgreSQL trở thành bottleneck (traffic cao), migrate sang Redis driver. API không thay đổi gì, chỉ đổi `QUEUE_CONNECTION=redis`.

---

### 4. Write-behind Cache + Eventual Consistency cho Order

**Mục tiêu:** Phản hồi user ngay lập tức (~50ms) thay vì chờ toàn bộ DB transaction + email.

#### Tại sao phải trừ tồn kho trên Redis TRƯỚC khi ném vào Queue?

Nếu chỉ ném `POST /orders` thẳng vào Queue mà không trừ tồn kho tạm thời trên Redis: 1000 user cùng mua 1 sản phẩm còn 1 cái sẽ đều lọt qua validation, Queue nhận 1000 jobs, worker chạy thì 999 jobs báo lỗi hết hàng — nhưng đã lỡ trả "Thành công" cho 1000 người. Đó là overselling thảm họa.

Redis atomic decrement (`DECRBY`) chính là thao tác "giữ hàng" — chốt chặn siêu tốc ở tiền sảnh, chặn các request sau ngay lập tức mà không cần chạm DB.

#### Flow

```
POST /orders
    ↓
Redis: DECRBY inventory:variant:{id} {quantity}  ← giữ hàng, atomic
    ↓ nếu kết quả < 0 → INCRBY hoàn lại, trả lỗi "hết hàng"
    ↓ nếu OK
Tạo order object với UUID, lưu tạm vào Redis
Trả response đầy đủ cho user ngay (~50ms)
    ↓
Queue job: ghi orders, order_items vào PostgreSQL, gửi email, notify warehouse
    ↓
Nếu job fail → Compensation job: INCRBY hoàn inventory Redis, notify user
```

#### Vai trò của `inventory_reservations` trong kiến trúc mới

Bảng `inventory_reservations` **có thể bỏ** — thông tin "đơn nào đang giữ bao nhiêu hàng" có thể query trực tiếp từ:

```sql
SELECT SUM(quantity) FROM order_items
JOIN orders ON orders.id = order_items.order_id
WHERE orders.status = 'pending'
AND order_items.product_variant_id = ?
```

Nghiệp vụ "giữ hàng" vẫn tồn tại, chỉ **chuyển hộ khẩu** từ DB (đồng bộ) lên Redis (bất đồng bộ):

| Việc | Trước | Sau |
|---|---|---|
| Prevent oversell | pessimistic lock + reservation table | Redis atomic decrement |
| Audit trail | `inventory_reservations` | query từ `order_items + orders.status` |
| Hoàn hàng khi timeout | release reservation | Queue job: INCRBY Redis + update order |
| Compensation khi fail | không có | Compensation job: INCRBY Redis |
| Rebuild Redis khi restart | không có | tính từ `order_items` WHERE status=pending |

#### Hai bài toán cốt lõi vẫn phải giải quyết

**Payment timeout (user không thanh toán):**
`ReleaseExpiredReservations` job quét các đơn `pending` quá 15 phút → INCRBY Redis, update order status = `cancelled`.

**Compensation khi DB fail:**
Job ghi DB thất bại sau khi đã trả response thành công → Compensation job INCRBY Redis hoàn lại, notify user đơn bị hủy.

#### Những thứ cần giải quyết trước khi implement

- **Idempotency key:** Job retry không được tạo 2 đơn hàng
- **Redis AOF persistence:** Giảm window mất data khi Redis restart
- **Rebuild strategy:** Khi Redis restart, tính lại tồn kho từ `inventories.quantity - SUM(order_items.quantity WHERE orders.status=pending)`

#### Thứ tự implement

1. Redis cache inventory read (`inventory:variant:{id}`) — đơn giản, ít rủi ro
2. Async email/notification qua queue (database driver)
3. Write-behind cho order creation — phức tạp nhất, làm sau cùng

---

### 5. Dọn dẹp nhỏ

- `billing_address` trong `orders` hiện luôn = `shipping_address` — cho phép client truyền riêng hoặc bỏ đi
- `orders.code` đang generate bằng `Str::random()` ở Controller — vi phạm kiến trúc, nên chuyển vào Domain layer

---

## Sơ đồ DB hiện tại

```
users
 ├── addresses
 ├── carts → cart_items → product_variants
 ├── wishlists → products
 └── orders → order_items → product_variants
                           → warehouses
            → shipments → shipping_providers
                        → shipment_status_histories

products → product_variants → inventories → warehouses
                             → inventory_reservations → orders  (sẽ bỏ)
categories → products
```

### Các bảng và trạng thái

| Bảng                     | Ghi chú                                                            |
| --------------------------| --------------------------------------------------------------------|
| `users`                  | bigint ID — cần đổi UUID v7                                        |
| `categories`             | self-referencing (parent_id), softDeletes                          |
| `products`               | `specs` JSON, softDeletes                                          |
| `product_variants`       | `images` + `options` JSON, softDeletes                             |
| `inventories`            | unique(warehouse_id, product_variant_id)                           |
| `inventory_reservations` | sẽ bỏ khi implement write-behind                                   |
| `orders`                 | `shipping_address` + `billing_address` JSONB snapshot, softDeletes |
| `order_items`            | `product_snapshot` JSONB, có `subtotal`                            |
| `carts` / `cart_items`   | đơn giản, ổn                                                       |
| `addresses`              | `is_default` flag                                                  |