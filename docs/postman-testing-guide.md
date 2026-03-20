# Postman Testing Guide — Zent API

Base URL: `http://localhost/api/v1`

Tất cả request cần header:
```
Accept: application/json
Content-Type: application/json
```

Các route protected cần thêm:
```
Authorization: Bearer {token}
```

---

## 1. Auth

### 1.1 Register
```
POST /api/v1/register
```
Body:
```json
{
  "name": "Nguyen Van A",
  "email": "test@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!"
}
```
Kết quả mong đợi: `201` — trả về user + token

---

### 1.2 Login
```
POST /api/v1/login
```
Body:
```json
{
  "email": "test@example.com",
  "password": "Password123!"
}
```
Kết quả mong đợi: `200` — trả về `{ success: true, data: { token, user } }`

> Lưu token lại dùng cho các request tiếp theo.

---

### 1.3 Get current user
```
GET /api/v1/me
Authorization: Bearer {token}
```
Kết quả mong đợi: `200` — thông tin user hiện tại

---

### 1.4 Logout
```
POST /api/v1/logout
Authorization: Bearer {token}
```
Kết quả mong đợi: `200` — `{ success: true, message: "..." }`

---

## 2. Products

### 2.1 List products (có pagination + filter)
```
GET /api/v1/products
GET /api/v1/products?page=1&per_page=10
GET /api/v1/products?search=áo&category_id=1
GET /api/v1/products?is_active=1
```
Kết quả mong đợi: `200` — `{ success, data: [...], meta: { current_page, per_page, total, last_page } }`

---

### 2.2 Get product by ID
```
GET /api/v1/products/1
```
Kết quả mong đợi: `200` — product với variants

Error case: ID không tồn tại → `404` — `{ success: false, code: "PRODUCT_NOT_FOUND" }`

---

### 2.3 Get product variants
```
GET /api/v1/products/1/variants
```

---

### 2.4 Create product (admin)
```
POST /api/v1/products
Authorization: Bearer {admin_token}
```
Body:
```json
{
  "name": "Áo thun nam",
  "slug": "ao-thun-nam",
  "description": "Mô tả sản phẩm",
  "category_id": 1,
  "is_active": true
}
```
Kết quả mong đợi: `201`

---

### 2.5 Update product (admin)
```
PUT /api/v1/products/1
Authorization: Bearer {admin_token}
```
Body: các field cần update

---

### 2.6 Delete product (admin)
```
DELETE /api/v1/products/1
Authorization: Bearer {admin_token}
```
Kết quả mong đợi: `200` — `{ success: true, message: "Product deleted successfully" }`

---

## 3. Categories

### 3.1 List categories
```
GET /api/v1/categories
```

### 3.2 Category tree
```
GET /api/v1/categories/tree
```
Kết quả mong đợi: nested tree structure

### 3.3 Get category
```
GET /api/v1/categories/1
```

### 3.4 Create category (admin)
```
POST /api/v1/categories
Authorization: Bearer {admin_token}
```
Body:
```json
{
  "name": "Áo",
  "slug": "ao",
  "parent_id": null
}
```

---

## 4. Cart

### 4.1 Get cart
```
GET /api/v1/cart
Authorization: Bearer {token}
```

### 4.2 Add item to cart
```
POST /api/v1/cart/items
Authorization: Bearer {token}
```
Body:
```json
{
  "product_variant_id": 1,
  "quantity": 2
}
```
Kết quả mong đợi: `201`

Error case: variant không tồn tại → `422`

### 4.3 Update cart item
```
PUT /api/v1/cart/items/{itemId}
Authorization: Bearer {token}
```
Body:
```json
{
  "quantity": 3
}
```

### 4.4 Remove cart item
```
DELETE /api/v1/cart/items/{itemId}
Authorization: Bearer {token}
```

### 4.5 Clear cart
```
DELETE /api/v1/cart/clear
Authorization: Bearer {token}
```

---

## 5. Addresses

### 5.1 List addresses
```
GET /api/v1/addresses
Authorization: Bearer {token}
```

### 5.2 Create address
```
POST /api/v1/addresses
Authorization: Bearer {token}
```
Body:
```json
{
  "label": "Nhà",
  "recipient_name": "Nguyen Van A",
  "phone": "0901234567",
  "address_line_1": "123 Đường ABC",
  "address_line_2": "Phường XYZ",
  "city": "Hồ Chí Minh",
  "postal_code": "70000",
  "country": "VN",
  "is_default": true
}
```

### 5.3 Set default address
```
POST /api/v1/addresses/{address}/set-default
Authorization: Bearer {token}
```

---

## 6. Orders — Luồng đầy đủ

> Đây là luồng quan trọng nhất, test theo thứ tự.

### Bước 1: Kiểm tra inventory trước
```
GET /api/v1/variants/{variantId}/inventory
```
Kết quả: xem `quantity` còn bao nhiêu

### Bước 2: Tạo order
```
POST /api/v1/orders
Authorization: Bearer {token}
```
Body:
```json
{
  "total_amount": 250000,
  "shipping_address": {
    "name": "Nguyen Van A",
    "phone": "0901234567",
    "address": "123 Đường ABC, Phường XYZ, TP.HCM"
  },
  "notes": "Giao giờ hành chính",
  "items": [
    {
      "product_variant_id": 1,
      "warehouse_id": 1,
      "quantity": 2,
      "price": 100000,
      "subtotal": 200000,
      "product_snapshot": {
        "name": "Áo thun nam - Size M",
        "sku": "ATN-M-001"
      }
    }
  ]
}
```
Kết quả mong đợi: `201` — order với `code` do server gen (dạng `ORD-XXXXXXXXXX`)

Error cases:
- Không đủ hàng → `422` — `{ code: "INSUFFICIENT_STOCK" }`
- Variant không tồn tại → `422`

### Bước 3: Kiểm tra inventory sau khi đặt
```
GET /api/v1/variants/{variantId}/inventory
```
Kết quả: `quantity` phải giảm đúng số lượng đã đặt

### Bước 4: Xem danh sách orders
```
GET /api/v1/orders
GET /api/v1/orders?page=1&per_page=5
GET /api/v1/orders?status=pending
Authorization: Bearer {token}
```
Kết quả: chỉ thấy orders của user hiện tại (không thấy của người khác)

### Bước 5: Xem chi tiết order
```
GET /api/v1/orders/{id}
Authorization: Bearer {token}
```

### Bước 6: Cancel order
```
POST /api/v1/orders/{id}/cancel
Authorization: Bearer {token}
```
Kết quả mong đợi: `200` — status chuyển sang `cancelled`

Sau đó kiểm tra lại inventory:
```
GET /api/v1/variants/{variantId}/inventory
```
Kết quả: `quantity` phải được cộng lại (reservation released)

---

## 7. Inventory (admin)

### 7.1 List inventory
```
GET /api/v1/inventory
Authorization: Bearer {admin_token}
```

### 7.2 Low stock alert
```
GET /api/v1/inventory/low-stock
GET /api/v1/inventory/low-stock/5
Authorization: Bearer {admin_token}
```
Kết quả: các items có `quantity <= threshold`

### 7.3 Inventory by warehouse
```
GET /api/v1/inventory/warehouse/{warehouseId}
Authorization: Bearer {admin_token}
```

### 7.4 Adjust inventory
```
POST /api/v1/inventory/{id}/adjust
Authorization: Bearer {admin_token}
```
Body:
```json
{
  "adjustment": 10,
  "reason": "Nhập hàng mới"
}
```
Điều chỉnh âm để trừ:
```json
{
  "adjustment": -5,
  "reason": "Hàng hỏng"
}
```
Error case: adjustment âm vượt quá quantity hiện tại → `422` — `{ code: "INSUFFICIENT_INVENTORY" }`

---

## 8. Shipping

### 8.1 Get providers
```
GET /api/v1/shipping/providers
```

### 8.2 Get settings
```
GET /api/v1/shipping/settings
```

### 8.3 Calculate fees (GHTK)
```
POST /api/v1/shipping/calculate-fees
```
Body:
```json
{
  "from_province": "Hồ Chí Minh",
  "from_district": "Quận 1",
  "to_province": "Hà Nội",
  "to_district": "Hoàn Kiếm",
  "weight": 500,
  "value": 200000,
  "transport": "road"
}
```

### 8.4 Calculate fees (GHN)
```
POST /api/v1/shipping/calculate-fees
```
Body:
```json
{
  "from_province": "Hồ Chí Minh",
  "from_district": "Quận 1",
  "to_province": "Hà Nội",
  "to_district": "Hoàn Kiếm",
  "from_district_id": 1442,
  "from_ward_code": "20308",
  "to_district_id": 1574,
  "to_ward_code": "550113",
  "weight": 500,
  "value": 200000
}
```
Kết quả mong đợi: `{ success: true, data: { fees: [...], cheapest: {...} } }`

---

## 9. Shipment Tracking

> Cần order đã tồn tại. Provider phải được cấu hình trong DB.

### 9.1 Create shipment
```
POST /api/v1/orders/{orderId}/shipment
Authorization: Bearer {token}
```
Body (GHTK format):
```json
{
  "provider_code": "ghtk",
  "order_data": {
    "order": {
      "id": "ORD-ABC123",
      "pick_name": "Cửa hàng ABC",
      "pick_tel": "0901234567",
      "pick_address": "123 Nguyễn Huệ",
      "pick_province": "Hồ Chí Minh",
      "pick_district": "Quận 1",
      "name": "Nguyen Van A",
      "tel": "0987654321",
      "address": "456 Lê Lợi",
      "province": "Hà Nội",
      "district": "Hoàn Kiếm",
      "value": 200000,
      "pick_money": 200000,
      "is_freeship": "0",
      "note": "Giao cẩn thận"
    },
    "products": [
      {
        "name": "Áo thun nam",
        "weight": 0.5,
        "quantity": 2
      }
    ]
  }
}
```
Kết quả mong đợi: `201`

Error cases:
- Order không tồn tại → `404` — `{ code: "ORDER_NOT_FOUND" }`
- Shipment đã tồn tại → `409` — `{ code: "SHIPMENT_ALREADY_EXISTS" }`

### 9.2 Get shipment status
```
GET /api/v1/orders/{orderId}/shipment
Authorization: Bearer {token}
```
Kết quả: shipment với `status_histories`

### 9.3 Cancel shipment
```
POST /api/v1/orders/{orderId}/shipment/cancel
Authorization: Bearer {token}
```
Kết quả mong đợi: `200` — status chuyển sang `cancelled`

Error case: shipment đang `in_transit` → `422` — `{ code: "SHIPMENT_CANNOT_BE_CANCELLED" }`

---

## 10. Banners

### 10.1 List all banners
```
GET /api/v1/banners
```

### 10.2 Active banners
```
GET /api/v1/banners/active
```

### 10.3 Banners by position
```
GET /api/v1/banners/position/home_top
GET /api/v1/banners/position/home_middle
```

### 10.4 Create banner (admin)
```
POST /api/v1/banners
Authorization: Bearer {admin_token}
```
Body:
```json
{
  "title": "Banner khuyến mãi",
  "image_url": "https://example.com/banner.jpg",
  "link_url": "https://example.com/sale",
  "position": "home_top",
  "is_active": true,
  "sort_order": 1
}
```

---

## 11. Wishlist

### 11.1 Get wishlist
```
GET /api/v1/wishlist
Authorization: Bearer {token}
```

### 11.2 Add to wishlist
```
POST /api/v1/wishlist
Authorization: Bearer {token}
```
Body:
```json
{
  "product_id": 1
}
```

### 11.3 Check if in wishlist
```
GET /api/v1/wishlist/check/{productId}
Authorization: Bearer {token}
```

### 11.4 Remove from wishlist
```
DELETE /api/v1/wishlist/{productId}
Authorization: Bearer {token}
```

---

## 12. Notifications

### 12.1 List notifications
```
GET /api/v1/notifications
Authorization: Bearer {token}
```

### 12.2 Unread count
```
GET /api/v1/notifications/unread-count
Authorization: Bearer {token}
```

### 12.3 Mark as read
```
POST /api/v1/notifications/{id}/read
Authorization: Bearer {token}
```

### 12.4 Mark all as read
```
POST /api/v1/notifications/read-all
Authorization: Bearer {token}
```

---

## Error Response Format

Tất cả lỗi đều theo format:
```json
{
  "success": false,
  "code": "ERROR_CODE",
  "message": "Mô tả lỗi"
}
```

| Code | HTTP | Ý nghĩa |
|---|---|---|
| `INSUFFICIENT_STOCK` | 422 | Không đủ hàng khi đặt |
| `ORDER_NOT_FOUND` | 404 | Order không tồn tại |
| `PRODUCT_NOT_FOUND` | 404 | Product không tồn tại |
| `SHIPMENT_NOT_FOUND` | 404 | Chưa có shipment cho order này |
| `SHIPMENT_ALREADY_EXISTS` | 409 | Order đã có shipment rồi |
| `SHIPMENT_CANNOT_BE_CANCELLED` | 422 | Shipment không thể hủy ở trạng thái hiện tại |
| `INSUFFICIENT_INVENTORY` | 422 | Adjust inventory âm vượt quá số tồn |
| `ADDRESS_NOT_FOUND` | 404 | Địa chỉ không tồn tại |
| `UNAUTHORIZED_ADDRESS_ACCESS` | 403 | Không có quyền truy cập địa chỉ này |

---

## Postman Collection Setup

1. Tạo Collection `Zent API`
2. Tạo biến Collection:
   - `base_url` = `http://localhost/api/v1`
   - `token` = (để trống, tự điền sau khi login)
   - `admin_token` = (để trống)
3. Trong request Login, thêm script vào tab **Tests**:
```javascript
const res = pm.response.json();
if (res.success) {
    pm.collectionVariables.set("token", res.data.token);
}
```
4. Dùng `{{base_url}}` và `{{token}}` trong các request

