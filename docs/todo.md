# Zent API — Phân tích & Việc cần làm

## Đã hoàn thành

| # | Việc | Ghi chú |
|---|---|---|
| 1.1 | Fix security hole `CreateOrderRequest` | Bỏ `user_id`, `code` khỏi request |
| 1.2 | Fix `CancelOrderAction` — release reservation | Cộng lại inventory khi cancel |
| 1.4 | Implement `InventoryReservation` trong order flow | Tạo reservation khi đặt hàng |
| 1.5 | Scheduled job expire reservation | `ReleaseExpiredReservationsJob` + `CancelUnpaidOrdersJob` |
| 2 | Đăng ký routes còn thiếu | Banner, Inventory, Product/Category write, Shipment |
| 3.1 | Move `ShippingService` → Infrastructure | Fix layer violation |
| 4.1 | Fix GHN calculate fee | Thêm fields vào `ShippingCalculationData` + `toParams()` |
| 5.1 | Shipment tracking API | POST/GET/cancel `/orders/{order}/shipment` |
| 7 | Jobs moved to Infrastructure | Fix layer violation cho Jobs |
| 8 | Pagination cho product + order list | `per_page`, `page`, filters |

---

## Còn lại

### 1.3 Cart — không check tồn kho (tùy chọn)

`AddCartItemAction` không check còn hàng khi add vào giỏ. Business decision:
- Chấp nhận: user biết khi checkout
- Strict: check ngay khi add

---

### 5.2 Payment (lớn)

Không có domain, model, hay route nào liên quan đến thanh toán.
- COD only thì không cần làm gì thêm
- Tích hợp VNPay/Momo/ZaloPay nếu cần

---

### 5.3 Product filter chưa được truyền xuống (nhỏ)

`ProductController::index()` đã có pagination nhưng `show()` dùng `int $id` — chưa hỗ trợ lookup bằng slug. Route hiện tại là `{identifier}` nhưng controller nhận `int $id`.

---

### 5.4 Coupon/Discount

Không có. Cần thiết kế từ đầu nếu muốn.

---

### 5.5 Review/Rating

Không có. Cần thiết kế từ đầu nếu muốn.

---

### 5.6 ProductVariant CRUD

Chỉ có read. Không thể tạo/sửa/xóa variant qua API.

---

### 5.7 Warehouse routes

Có `GetWarehousesAction`, `GetWarehouseByIdAction` nhưng chưa có route nào expose ra API.

---

### 6. Admin Panel (Filament) còn thiếu

| Resource | Trạng thái |
|---|---|
| Banners | ❌ chưa có |
| Shipping Providers | ❌ chưa có |
| Dashboard widgets | ❌ trống |

---

### Tests

Chưa có test nào. Ưu tiên viết test cho:
1. Order flow (create + inventory reservation + cancel)
2. Inventory check (insufficient stock)
3. Shipping fee calculation
4. Auth (register/login)

