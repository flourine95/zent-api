# Module Banner - Refactoring Summary

## Tổng quan
Module Banner là module đơn giản nhất, chủ yếu CRUD với một số business rules về date range và active status.

## Đặc điểm nghiệp vụ

### Core Features
- **CRUD cơ bản**: Create, Read, Update, Delete banners
- **Active scope**: Chỉ hiển thị banner active và trong date range
- **Position-based**: Filter banner theo vị trí (home_hero, home_secondary, etc.)
- **Order sorting**: Sắp xếp theo field 'order' và created_at

### Business Rules
1. **Date range validation**: start_date phải < end_date
2. **Active logic**: Banner active khi:
   - `is_active = true`
   - `start_date` null hoặc <= now()
   - `end_date` null hoặc >= now()
3. **Position enum**: Chỉ cho phép 4 positions cố định

## Domain Layer (8 files)

### DTOs (2 files)

**CreateBannerData**
```php
public function __construct(
    public string $title,
    public ?string $description,
    public string $image,
    public ?string $link,
    public ?string $buttonText,
    public string $position,
    public int $order,
    public bool $isActive,
    public ?DateTimeInterface $startDate,
    public ?DateTimeInterface $endDate,
) {}
```

**UpdateBannerData** - Giống CreateBannerData + thêm `id`

### Actions (3 files)

**CreateBannerAction**
- Validate date range (start < end)
- Gọi repository create

**UpdateBannerAction**
- Check banner exists
- Validate date range
- Gọi repository update

**DeleteBannerAction**
- Check banner exists
- Gọi repository delete

### Repository Interface (1 file)

```php
interface BannerRepositoryInterface
{
    public function create(array $data): array;
    public function update(int $id, array $data): array;
    public function delete(int $id): bool;
    public function findById(int $id): ?array;
    public function exists(int $id): bool;
    public function getAll(): array;
    public function getActive(): array;
    public function getByPosition(string $position): array;
}
```

### Exceptions (2 files)

- `BannerNotFoundException` - Banner không tồn tại
- `InvalidBannerException` - Date range không hợp lệ

## Infrastructure Layer (2 files)

### Model (1 file)

**Banner Model**
- Eloquent model với casts cho dates và boolean
- Scope `active()` - Filter active banners
- Scope `position()` - Filter by position

```php
public function scopeActive($query)
{
    return $query->where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('start_date')
                ->orWhere('start_date', '<=', now());
        })
        ->where(function ($q) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>=', now());
        });
}
```

### Repository (1 file)

**EloquentBannerRepository**
- Implement tất cả methods từ interface
- `getActive()` - Dùng scope active()
- `getByPosition()` - Combine active() + position()
- Sort by 'order' field

## App Layer (3 files)

### Controller (1 file)

**BannerController** - 7 methods:
- `index()` - Get all banners
- `active()` - Get active banners only
- `byPosition(string $position)` - Get by position
- `show(int $id)` - Get single banner
- `store(CreateBannerRequest)` - Create new
- `update(UpdateBannerRequest, int $id)` - Update existing
- `destroy(int $id)` - Delete banner

### Requests (2 files)

**CreateBannerRequest**
```php
'position' => ['required', 'string', 'in:home_hero,home_secondary,category_top,product_detail'],
'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
```

**UpdateBannerRequest** - Giống CreateBannerRequest

## Logic đặc biệt

### 1. Active Scope với Date Range
```php
// Repository
public function getActive(): array
{
    return Banner::active()->orderBy('order')->get()->toArray();
}

// Model scope
public function scopeActive($query)
{
    return $query->where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('start_date')->orWhere('start_date', '<=', now());
        })
        ->where(function ($q) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', now());
        });
}
```

### 2. Date Range Validation
```php
// Action
if ($data->startDate && $data->endDate) {
    if ($data->startDate > $data->endDate) {
        throw InvalidBannerException::invalidDateRange();
    }
}
```

### 3. Position-based Filtering
```php
public function getByPosition(string $position): array
{
    return Banner::active()
        ->position($position)
        ->orderBy('order')
        ->get()
        ->toArray();
}
```

## Files đã update (5 files)

1. `database/factories/BannerFactory.php`
2. `database/seeders/BannerSeeder.php`
3. `app/Observers/BannerObserver.php`
4. `app/Filament/Resources/Banners/BannerResource.php`
5. `app/Http/Controllers/Api/ConfigController.php`
6. `app/Providers/AppServiceProvider.php` (Observer registration)

## Service Provider Binding

```php
// app/Providers/DomainServiceProvider.php
public array $bindings = [
    BannerRepositoryInterface::class => EloquentBannerRepository::class,
    // ...
];
```

## Thống kê

- **Total files created**: 13 files
- **Domain Layer**: 8 files (~300 lines)
- **Infrastructure Layer**: 2 files (~100 lines)
- **App Layer**: 3 files (~150 lines)
- **Files updated**: 6 files
- **Time taken**: ~30 minutes

## Lessons Learned

### ✅ What Worked Well

1. **Simple module first**: Banner là module đơn giản, làm nhanh để tăng momentum
2. **Scope reuse**: Eloquent scopes giúp code clean và reusable
3. **DateTime handling**: DTO với DateTimeInterface giúp type-safe
4. **Position enum**: Validate position ở Request level

### 💡 Improvements

1. **Position enum class**: Nên tạo enum class thay vì hardcode strings
2. **Image validation**: Nên validate image format/size ở Request
3. **Caching**: Active banners có thể cache vì ít thay đổi

## Next Steps

Module Banner đã hoàn thành. Còn lại 2 modules:

1. **Inventory** (Medium) - Stock management, reservations
2. **User/Auth** (High) - Authentication, roles, permissions

Recommend: Làm Inventory trước vì User/Auth phức tạp nhất.
