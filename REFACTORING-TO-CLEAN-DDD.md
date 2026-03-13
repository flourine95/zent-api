# Refactoring to Clean Domain-Driven Design Architecture

## ✅ Refactoring Hoàn Thành!

Cấu trúc DDD đã được refactor thành công vào ngày: **${new Date().toLocaleDateString('vi-VN')}**

### Kết quả cuối cùng:

```
app/
├── App/                     # ✅ Application Layer (Controllers, Requests)
├── Domain/                  # ✅ Domain Layer (Actions, DTOs, Repositories Interfaces)
│   ├── Order/
│   │   ├── Jobs/           # ✅ Order-specific jobs
│   │   ├── Notifications/  # ✅ Order-specific notifications
│   │   └── Policies/       # ✅ Order-specific policies
│   ├── Product/
│   │   ├── Observers/      # ✅ Product-specific observers
│   │   └── Policies/       # ✅ Product-specific policies
│   ├── Shipping/
│   │   ├── Contracts/      # ✅ ShippingProviderInterface
│   │   └── Services/       # ✅ ShippingService (domain logic)
│   └── ...
├── Infrastructure/          # ✅ Infrastructure Layer
│   ├── Models/             # ✅ Eloquent Models
│   ├── Repositories/       # ✅ Repository Implementations
│   └── ExternalServices/   # ✅ Third-party integrations
│       ├── Ghn/
│       ├── Ghtk/
│       └── ShippingProviderFactory.php
├── Presentation/            # ✅ Presentation Layer (UI)
│   └── Filament/           # ✅ Admin panel
└── Shared/                  # ✅ Shared Kernel
    ├── Console/
    └── Providers/
```

---

## Vấn đề hiện tại

Cấu trúc folder đang bị **"lai căng"** giữa Laravel truyền thống và DDD:

### ❌ Các thành phần đặt sai vị trí:

```
app/
├── Jobs/                    # ❌ Nên thuộc Domain cụ thể
├── Notifications/           # ❌ Nên thuộc Domain cụ thể  
├── Observers/               # ❌ Nên thuộc Domain cụ thể
├── Policies/                # ❌ Nên thuộc Domain cụ thể
├── Services/                # ❌ Shipping services nên vào Infrastructure
├── Contracts/               # ❌ Nên thuộc Domain cụ thể
└── Filament/                # ❌ UI layer nên tách riêng
```

## Cấu trúc DDD lý tưởng

### ✅ Cấu trúc đề xuất (4 tầng rõ ràng):

```
app/
├── App/                     # Application Layer (Controllers, Requests, Middleware)
│   ├── Address/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Order/
│   │   ├── Controllers/
│   │   └── Requests/
│   └── ...
│
├── Domain/                  # Domain Layer (Business Logic)
│   ├── Address/
│   │   ├── Actions/
│   │   ├── DataTransferObjects/
│   │   ├── Exceptions/
│   │   ├── Repositories/    # Interfaces only
│   │   └── Models/          # Domain Models (optional)
│   │
│   ├── Order/
│   │   ├── Actions/
│   │   ├── DataTransferObjects/
│   │   ├── Exceptions/
│   │   ├── Repositories/
│   │   ├── Jobs/            # ✅ Order-specific jobs
│   │   ├── Notifications/   # ✅ Order-specific notifications
│   │   ├── Observers/       # ✅ Order-specific observers
│   │   ├── Policies/        # ✅ Order-specific policies
│   │   └── Contracts/       # ✅ Order-specific contracts
│   │
│   ├── Shipping/
│   │   ├── Actions/
│   │   ├── Contracts/       # ✅ ShippingProviderInterface
│   │   ├── Services/        # ✅ ShippingService (domain logic)
│   │   └── ...
│   └── ...
│
├── Infrastructure/          # Infrastructure Layer (Technical Implementation)
│   ├── Persistence/
│   │   ├── Models/          # Eloquent Models
│   │   └── Repositories/    # Repository Implementations
│   │
│   ├── ExternalServices/
│   │   ├── Ghn/
│   │   │   ├── GhnService.php
│   │   │   └── GhnOrderBuilder.php
│   │   ├── Ghtk/
│   │   │   ├── GhtkService.php
│   │   │   └── GhtkOrderBuilder.php
│   │   └── ShippingProviderFactory.php
│   │
│   └── Cache/               # Cache implementations
│
├── Presentation/            # Presentation Layer (UI)
│   ├── Filament/
│   │   ├── Resources/
│   │   ├── Pages/
│   │   ├── Widgets/
│   │   ├── Exports/
│   │   └── Imports/
│   │
│   └── Api/                 # API-specific presenters (if needed)
│
└── Shared/                  # Shared Kernel
    ├── Providers/
    ├── Console/
    └── Helpers/
```

## Roadmap Refactoring

### Phase 1: Di chuyển Jobs, Notifications, Observers, Policies vào Domain

#### 1.1 Order Domain
```bash
# Jobs
app/Jobs/CancelUnpaidOrders.php 
  → app/Domain/Order/Jobs/CancelUnpaidOrdersJob.php

app/Jobs/ReleaseExpiredReservations.php 
  → app/Domain/Order/Jobs/ReleaseExpiredReservationsJob.php

# Notifications
app/Notifications/OrderCreatedNotification.php 
  → app/Domain/Order/Notifications/OrderCreatedNotification.php

app/Notifications/OrderStatusChangedNotification.php 
  → app/Domain/Order/Notifications/OrderStatusChangedNotification.php

# Policies
app/Policies/OrderPolicy.php 
  → app/Domain/Order/Policies/OrderPolicy.php
```

#### 1.2 Product Domain
```bash
# Observers
app/Observers/ProductObserver.php 
  → app/Domain/Product/Observers/ProductObserver.php

# Policies
app/Policies/ProductPolicy.php 
  → app/Domain/Product/Policies/ProductPolicy.php
```

#### 1.3 Category Domain
```bash
# Observers
app/Observers/CategoryObserver.php 
  → app/Domain/Category/Observers/CategoryObserver.php

# Policies
app/Policies/CategoryPolicy.php 
  → app/Domain/Category/Policies/CategoryPolicy.php
```

#### 1.4 Banner Domain
```bash
# Observers
app/Observers/BannerObserver.php 
  → app/Domain/Banner/Observers/BannerObserver.php
```

#### 1.5 User Domain
```bash
# Policies
app/Policies/UserPolicy.php 
  → app/Domain/User/Policies/UserPolicy.php
```

#### 1.6 Warehouse Domain
```bash
# Policies
app/Policies/WarehousePolicy.php 
  → app/Domain/Warehouse/Policies/WarehousePolicy.php
```

#### 1.7 Config Domain
```bash
# Observers
app/Observers/SettingObserver.php 
  → app/Domain/Config/Observers/SettingObserver.php
```

### Phase 2: Tái cấu trúc Shipping Services

```bash
# Contracts
app/Contracts/ShippingProviderInterface.php 
  → app/Domain/Shipping/Contracts/ShippingProviderInterface.php

# Domain Service
app/Services/ShippingService.php 
  → app/Domain/Shipping/Services/ShippingService.php

# Infrastructure Services
app/Services/GhnService.php 
  → app/Infrastructure/ExternalServices/Ghn/GhnService.php

app/Services/GhnOrderBuilder.php 
  → app/Infrastructure/ExternalServices/Ghn/GhnOrderBuilder.php

app/Services/GhtkService.php 
  → app/Infrastructure/ExternalServices/Ghtk/GhtkService.php

app/Services/GhtkOrderBuilder.php 
  → app/Infrastructure/ExternalServices/Ghtk/GhtkOrderBuilder.php

app/Services/ShippingProviderFactory.php 
  → app/Infrastructure/ExternalServices/ShippingProviderFactory.php
```

### Phase 3: Tách Filament ra Presentation Layer

```bash
app/Filament/ 
  → app/Presentation/Filament/
```

### Phase 4: Đổi tên Shared/Support

```bash
app/Providers/ 
  → app/Shared/Providers/

app/Console/ 
  → app/Shared/Console/
```

## Lợi ích sau khi refactor

### ✅ Trước (Rối):
```
app/
├── Jobs/                    # 2 files
├── Notifications/           # 2 files
├── Observers/               # 4 files
├── Policies/                # 5 files
├── Services/                # 6 files
├── Contracts/               # 1 file
├── Filament/                # Nhiều files
└── 12 folders khác...
```

### ✅ Sau (Gọn gàng):
```
app/
├── App/                     # Application Layer
├── Domain/                  # Business Logic (mọi thứ thuộc domain)
├── Infrastructure/          # Technical Implementation
├── Presentation/            # UI Layer
└── Shared/                  # Shared utilities
```

## Checklist thực hiện

### Bước 1: Backup
- [ ] Commit toàn bộ code hiện tại
- [ ] Tạo branch mới: `git checkout -b refactor/clean-ddd`

### Bước 2: Di chuyển từng Domain
- [ ] Order Domain (Jobs, Notifications, Policies)
- [ ] Product Domain (Observers, Policies)
- [ ] Category Domain (Observers, Policies)
- [ ] Banner Domain (Observers)
- [ ] User Domain (Policies)
- [ ] Warehouse Domain (Policies)
- [ ] Config Domain (Observers)

### Bước 3: Refactor Shipping
- [ ] Di chuyển Contracts vào Domain\Shipping
- [ ] Di chuyển Services vào Infrastructure\ExternalServices
- [ ] Cập nhật namespace và imports

### Bước 4: Tách Presentation
- [ ] Di chuyển Filament vào Presentation layer
- [ ] Cập nhật namespace trong Filament Resources

### Bước 5: Cleanup
- [ ] Xóa các folder rỗng (Jobs, Notifications, Observers, Policies, Services, Contracts)
- [ ] Cập nhật composer.json autoload nếu cần
- [ ] Run `composer dump-autoload`
- [ ] Run `php artisan optimize:clear`

### Bước 6: Testing
- [ ] Chạy test suite: `php artisan test`
- [ ] Kiểm tra Filament admin panel
- [ ] Kiểm tra API endpoints
- [ ] Kiểm tra jobs và notifications

## Lưu ý quan trọng

1. **Namespace**: Mỗi lần di chuyển file phải cập nhật namespace và tất cả imports
2. **Service Provider**: Cập nhật các bindings trong `DomainServiceProvider`
3. **Config**: Cập nhật `config/auth.php` cho policies
4. **Observer Registration**: Cập nhật nơi đăng ký observers
5. **Job Dispatch**: Kiểm tra nơi dispatch jobs có đúng namespace không

## Kết luận

Sau khi refactor, cấu trúc sẽ:
- ✅ Rõ ràng theo từng tầng (Layer)
- ✅ Mỗi Domain tự quản lý Jobs, Policies, Observers của mình
- ✅ Infrastructure tách biệt khỏi Domain
- ✅ Presentation (Filament) tách biệt khỏi Business Logic
- ✅ Dễ scale khi thêm Domain mới
- ✅ Dễ test từng Domain độc lập
