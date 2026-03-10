# Domain-Oriented Architecture

## Tổng quan

Dự án này tuân thủ kiến trúc **Domain-Oriented Design** - một biến thể thực dụng của Hexagonal Architecture được tối ưu cho Laravel. Mục tiêu: Tách biệt hoàn toàn code nghiệp vụ (Domain) khỏi code framework/database (Infrastructure).

## Cấu trúc thư mục

```
app/
├── App/                    # Tầng Delivery/HTTP
│   └── Category/
│       ├── Controllers/    # Nhận request, trả response
│       └── Requests/       # Form validation
│
├── Domain/                 # Tầng Business Logic (PHP thuần)
│   └── Category/
│       ├── Actions/        # Use cases nghiệp vụ
│       ├── DataTransferObjects/  # DTOs
│       ├── Repositories/   # Interfaces (KHÔNG implementation)
│       └── Exceptions/     # Domain exceptions
│
├── Infrastructure/         # Tầng Framework/Database
│   ├── Models/            # Eloquent Models
│   └── Repositories/      # Repository implementations
│
└── Support/               # Helpers & 3rd party integrations
```

## Luồng dữ liệu

```
Request 
  → Controller (App layer)
    → Validate (FormRequest)
    → Tạo DTO
    → Gọi Action (Domain layer)
      → Action gọi Repository Interface
        → Repository Implementation (Infrastructure layer)
          → Eloquent Model
        ← Trả data dạng array
      ← Action xử lý logic
    ← Controller format response
  ← Response
```

## Nguyên tắc THÉP

### Domain Layer
- ✅ PHP thuần, KHÔNG phụ thuộc framework
- ✅ Chứa toàn bộ business logic
- ✅ Chỉ định nghĩa Interface, KHÔNG implementation
- ❌ KHÔNG import `Illuminate\Database\Eloquent\Model`
- ❌ KHÔNG dùng Facade `DB`, `Cache`, etc.

### Infrastructure Layer
- ✅ Nơi DUY NHẤT chứa Eloquent Models
- ✅ Implement Repository Interfaces từ Domain
- ✅ Xử lý database queries, caching, external APIs

### App Layer
- ✅ Chỉ làm 3 việc: Validate → DTO → Action → Response
- ❌ KHÔNG viết logic tính toán
- ❌ KHÔNG gọi Eloquent trực tiếp
- ❌ KHÔNG query database

## Module Category (Ví dụ hoàn chỉnh)

### 1. Domain Layer

**DTO (Data Transfer Object)**
```php
// app/Domain/Category/DataTransferObjects/CreateCategoryData.php
final readonly class CreateCategoryData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public ?int $parentId,
        public ?string $image,
        public bool $isVisible,
    ) {}

    public static function fromArray(array $data): self { ... }
    public function toArray(): array { ... }
}
```

**Repository Interface**
```php
// app/Domain/Category/Repositories/CategoryRepositoryInterface.php
interface CategoryRepositoryInterface
{
    public function create(array $data): array;
    public function update(int $id, array $data): array;
    public function delete(int $id): bool;
    public function findById(int $id): ?array;
    public function exists(int $id): bool;
}
```

**Action (Business Logic)**
```php
// app/Domain/Category/Actions/CreateCategoryAction.php
final readonly class CreateCategoryAction
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(CreateCategoryData $data): array
    {
        // Validate parent exists
        if ($data->parentId !== null) {
            if (!$this->categoryRepository->exists($data->parentId)) {
                throw CategoryNotFoundException::withId($data->parentId);
            }
        }

        return $this->categoryRepository->create($data->toArray());
    }
}
```

### 2. Infrastructure Layer

**Repository Implementation**
```php
// app/Infrastructure/Repositories/EloquentCategoryRepository.php
final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function create(array $data): array
    {
        $category = Category::create($data);
        return $category->toArray();
    }

    public function findById(int $id): ?array
    {
        $category = Category::find($id);
        return $category?->toArray();
    }
}
```

**Model**
```php
// app/Infrastructure/Models/Category.php
namespace App\Infrastructure\Models;

class Category extends Model
{
    // Eloquent model như bình thường
}
```

### 3. App Layer

**Controller**
```php
// app/App/Category/Controllers/CategoryController.php
final class CategoryController
{
    public function __construct(
        private readonly CreateCategoryAction $createCategoryAction,
    ) {}

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $data = CreateCategoryData::fromArray($request->validated());
            $category = $this->createCategoryAction->execute($data);

            return response()->json(['data' => $category], 201);
        } catch (CategoryNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
```

### 4. Service Provider Binding

```php
// app/Providers/DomainServiceProvider.php
class DomainServiceProvider extends ServiceProvider
{
    public array $bindings = [
        CategoryRepositoryInterface::class => EloquentCategoryRepository::class,
    ];
}
```

Đăng ký trong `bootstrap/providers.php`:
```php
return [
    App\Providers\DomainServiceProvider::class,
    // ...
];
```

## Testing

### Unit Tests (Domain Layer)
```php
// tests/Unit/Domain/Category/CreateCategoryActionTest.php
test('creates category successfully', function () {
    $repository = Mockery::mock(CategoryRepositoryInterface::class);
    $action = new CreateCategoryAction($repository);

    $data = new CreateCategoryData(...);
    
    $repository->shouldReceive('create')
        ->once()
        ->andReturn(['id' => 1, ...]);

    $result = $action->execute($data);
    expect($result['id'])->toBe(1);
});
```

### Integration Tests
```php
// tests/Feature/Domain/Category/CategoryIntegrationTest.php
test('full category lifecycle works', function () {
    $createAction = app(CreateCategoryAction::class);
    $data = new CreateCategoryData(...);
    
    $category = $createAction->execute($data);
    expect($category['name'])->toBe('Electronics');
});
```

## Lợi ích

1. **Testability**: Domain logic dễ test với mock repositories
2. **Maintainability**: Business logic tách biệt, dễ đọc và sửa
3. **Flexibility**: Dễ thay đổi database/framework mà không ảnh hưởng logic
4. **Clear boundaries**: Mỗi layer có trách nhiệm rõ ràng
5. **Type safety**: DTOs đảm bảo type-safe data transfer

## Checklist khi tạo module mới

- [ ] Tạo DTOs trong `app/Domain/{Module}/DataTransferObjects/`
- [ ] Tạo Repository Interface trong `app/Domain/{Module}/Repositories/`
- [ ] Tạo Actions trong `app/Domain/{Module}/Actions/`
- [ ] Tạo Exceptions trong `app/Domain/{Module}/Exceptions/`
- [ ] Tạo Model trong `app/Infrastructure/Models/`
- [ ] Tạo Repository Implementation trong `app/Infrastructure/Repositories/`
- [ ] Binding Interface trong `DomainServiceProvider`
- [ ] Tạo Controllers trong `app/App/{Module}/Controllers/`
- [ ] Tạo FormRequests trong `app/App/{Module}/Requests/`
- [ ] Viết Unit Tests cho Actions
- [ ] Viết Integration Tests

## Modules đã refactor

- ✅ **Category** - Hoàn thành (CRUD + Tree structure)
- ✅ **Product** - Hoàn thành (CRUD + Category relationship + Specs)
- ✅ **Order** - Hoàn thành (CRUD + OrderItems + Transaction + Validation)
- ✅ **Banner** - Hoàn thành (CRUD + Active scope + Position + Date range)
- ✅ **Inventory** - Hoàn thành (CRUD + Stock adjustment + Low stock + Duplicate check)
- ✅ **User/Auth** - Hoàn thành (Authentication + Authorization + Profile + Spatie Permission + Sanctum)

### Module Order
**Đặc điểm:**
- Aggregate Root (Order + OrderItems)
- Transaction management
- Complex validation (total amount, status transitions)
- Multiple relationships (User, Items, Reservations)

**Files đã tạo:**
- Domain: 3 DTOs, 3 Actions, 1 Interface, 2 Exceptions
- Infrastructure: 2 Models (Order, OrderItem), 1 Repository
- App: 1 Controller, 2 Requests

**Logic nghiệp vụ:**
- Order phải có ít nhất 1 item
- Total amount phải bằng tổng subtotal của items
- Không thể cancel order đã completed hoặc cancelled
- Tạo order + items trong 1 transaction

### Module Banner
**Đặc điểm:**
- Simple CRUD
- Active scope với date range
- Position-based filtering
- Order sorting

**Files đã tạo:**
- Domain: 2 DTOs, 3 Actions, 1 Interface, 2 Exceptions
- Infrastructure: 1 Model, 1 Repository
- App: 1 Controller, 2 Requests

**Logic nghiệp vụ:**
- Validate start_date < end_date
- Active scope: is_active=true + date range check
- Get by position (home_hero, home_secondary, etc.)
- Order by 'order' field then created_at

### Module Inventory
**Đặc điểm:**
- Stock management
- Inventory adjustment (increase/decrease)
- Low stock alerts
- Duplicate prevention (warehouse + variant unique)

**Files đã tạo:**
- Domain: 2 DTOs, 3 Actions, 1 Interface, 3 Exceptions
- Infrastructure: 2 Models (Inventory, InventoryReservation), 1 Repository
- App: 1 Controller, 3 Requests

**Logic nghiệp vụ:**
- Không cho tạo duplicate inventory (same warehouse + variant)
- Adjust inventory: không cho quantity < 0
- Get low stock: filter by threshold
- Get by warehouse hoặc by product variant

---

## Hướng dẫn refactor module mới (Step-by-step)

### Bước 1: Tạo Domain Layer

```bash
# Tạo cấu trúc thư mục
app/Domain/{Module}/
├── Actions/
├── DataTransferObjects/
├── Repositories/
└── Exceptions/
```

**1.1. Tạo DTOs**
```php
// app/Domain/{Module}/DataTransferObjects/Create{Module}Data.php
final readonly class Create{Module}Data
{
    public function __construct(
        public string $field1,
        public ?string $field2,
        // ... các fields
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            field1: $data['field1'],
            field2: $data['field2'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'field1' => $this->field1,
            'field2' => $this->field2,
        ];
    }
}
```

**1.2. Tạo Repository Interface**
```php
// app/Domain/{Module}/Repositories/{Module}RepositoryInterface.php
interface {Module}RepositoryInterface
{
    public function create(array $data): array;
    public function update(int $id, array $data): array;
    public function delete(int $id): bool;
    public function findById(int $id): ?array;
    public function exists(int $id): bool;
}
```

**1.3. Tạo Actions**
```php
// app/Domain/{Module}/Actions/Create{Module}Action.php
final readonly class Create{Module}Action
{
    public function __construct(
        private {Module}RepositoryInterface $repository
    ) {}

    public function execute(Create{Module}Data $data): array
    {
        // Validate nghiệp vụ ở đây
        // Ví dụ: check foreign key exists
        
        return $this->repository->create($data->toArray());
    }
}
```

**1.4. Tạo Exceptions**
```php
// app/Domain/{Module}/Exceptions/{Module}NotFoundException.php
final class {Module}NotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("{Module} with ID {$id} not found.");
    }
}
```

### Bước 2: Tạo Infrastructure Layer

**2.1. Di chuyển Model**
```bash
# Dùng smartRelocate để tự động update imports
smartRelocate(
    sourcePath: "app/Models/{Module}.php",
    destinationPath: "app/Infrastructure/Models/{Module}.php"
)
```

**2.2. Update namespace trong Model**
```php
// app/Infrastructure/Models/{Module}.php
namespace App\Infrastructure\Models; // Đổi từ App\Models

class {Module} extends Model
{
    // Giữ nguyên code Eloquent
}
```

**2.3. Tạo Repository Implementation**
```php
// app/Infrastructure/Repositories/Eloquent{Module}Repository.php
final class Eloquent{Module}Repository implements {Module}RepositoryInterface
{
    public function create(array $data): array
    {
        $model = {Module}::create($data);
        return $model->toArray(); // ✅ Trả array, không trả Model
    }

    public function findById(int $id): ?array
    {
        $model = {Module}::find($id);
        return $model?->toArray();
    }

    public function exists(int $id): bool
    {
        return {Module}::where('id', $id)->exists();
    }
}
```

### Bước 3: Tạo App Layer

**3.1. Tạo FormRequests**
```php
// app/App/{Module}/Requests/Create{Module}Request.php
class Create{Module}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'field1' => ['required', 'string', 'max:255'],
            'field2' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'field1.required' => 'Field1 là bắt buộc.',
        ];
    }
}
```

**3.2. Tạo Controller**
```php
// app/App/{Module}/Controllers/{Module}Controller.php
final class {Module}Controller
{
    public function __construct(
        private readonly {Module}RepositoryInterface $repository,
        private readonly Create{Module}Action $createAction,
        private readonly Update{Module}Action $updateAction,
        private readonly Delete{Module}Action $deleteAction,
    ) {}

    public function store(Create{Module}Request $request): JsonResponse
    {
        try {
            $data = Create{Module}Data::fromArray($request->validated());
            $result = $this->createAction->execute($data);
            return response()->json(['data' => $result], 201);
        } catch ({Module}NotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
```

### Bước 4: Binding & Cleanup

**4.1. Update DomainServiceProvider**
```php
// app/Providers/DomainServiceProvider.php
public array $bindings = [
    // ... existing bindings
    {Module}RepositoryInterface::class => Eloquent{Module}Repository::class,
];
```

**4.2. Update tất cả namespace references**
```bash
# Tìm tất cả file import Model cũ
grepSearch("use App\\\\Models\\\\{Module};")

# Thay thế từng file
strReplace(
    path: "path/to/file.php",
    oldStr: "use App\\Models\\{Module};",
    newStr: "use App\\Infrastructure\\Models\\{Module};"
)
```

**4.3. Update AppServiceProvider (nếu có Observer)**
```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    \App\Infrastructure\Models\{Module}::observe(\App\Observers\{Module}Observer::class);
}
```

**4.4. Rebuild autoload**
```bash
composer dump-autoload
```

**4.5. Format code**
```bash
vendor/bin/pint --dirty --format agent
```

### Bước 5: Verify

**5.1. Kiểm tra không có lỗi autoload**
```bash
php artisan about
```

**5.2. Kiểm tra binding hoạt động**
```bash
php artisan tinker
>>> app(App\Domain\{Module}\Repositories\{Module}RepositoryInterface::class)
# Phải trả về instance của Eloquent{Module}Repository
```

## Lưu ý quan trọng khi refactor

### ✅ DO (Nên làm)
1. **Repository trả về array**, không trả Eloquent Model
2. **Actions validate nghiệp vụ** trước khi gọi repository
3. **Controller chỉ điều phối**: Validate → DTO → Action → Response
4. **Dùng `smartRelocate`** khi di chuyển Models
5. **Grep search trước khi replace** để tìm tất cả references
6. **Chạy Pint sau mỗi lần tạo file mới**

### ❌ DON'T (Không nên làm)
1. **KHÔNG viết logic nghiệp vụ trong Controller**
2. **KHÔNG import Eloquent trong Domain layer**
3. **KHÔNG dùng Facade trong Domain layer**
4. **KHÔNG skip bước dump-autoload**
5. **KHÔNG quên update Observer registration**

## Các vấn đề thường gặp & Giải pháp

### Lỗi: "Class not found"
**Nguyên nhân:** Chưa chạy `composer dump-autoload`
**Giải pháp:** 
```bash
composer dump-autoload
```

### Lỗi: "Target class does not exist"
**Nguyên nhân:** Chưa binding interface trong DomainServiceProvider
**Giải pháp:** Thêm binding và đảm bảo provider đã đăng ký trong `bootstrap/providers.php`

### Lỗi: "Call to undefined method"
**Nguyên nhân:** Repository Interface thiếu method
**Giải pháp:** Thêm method vào Interface và Implementation

### Model relationships không hoạt động
**Nguyên nhân:** Namespace trong relationship method chưa update
**Giải pháp:** 
```php
// Trong Model
public function category(): BelongsTo
{
    return $this->belongsTo(\App\Infrastructure\Models\Category::class);
}
```

## Pattern đặc biệt đã áp dụng

### 1. Validate Foreign Key trong Action
```php
// app/Domain/Product/Actions/CreateProductAction.php
public function execute(CreateProductData $data): array
{
    // ✅ Validate category exists
    if (!$this->categoryRepository->exists($data->categoryId)) {
        throw CategoryNotFoundException::withId($data->categoryId);
    }
    
    return $this->productRepository->create($data->toArray());
}
```

### 2. Prevent Circular Reference (Category)
```php
// app/Domain/Category/Actions/UpdateCategoryAction.php
public function execute(UpdateCategoryData $data): array
{
    // ✅ Cannot be its own parent
    if ($data->parentId === $data->id) {
        throw InvalidCategoryHierarchyException::selfReference($data->id);
    }
    
    // ✅ Cannot create circular reference
    if ($this->categoryRepository->isDescendantOf($data->parentId, $data->id)) {
        throw InvalidCategoryHierarchyException::circularReference($data->id, $data->parentId);
    }
}
```

### 3. Repository với eager loading
```php
// app/Infrastructure/Repositories/EloquentProductRepository.php
public function findById(int $id): ?array
{
    $product = Product::with(['category', 'variants'])->find($id);
    return $product?->toArray();
}
```

## Chi tiết modules đã refactor

### Module Category
**Đặc điểm:**
- Tree structure (parent-child relationship)
- Validate circular reference
- Sluggable

**Files đã tạo:**
- Domain: 2 DTOs, 3 Actions, 1 Interface, 2 Exceptions
- Infrastructure: 1 Model, 1 Repository
- App: 1 Controller, 2 Requests

**Logic nghiệp vụ:**
- Không cho category làm parent của chính nó
- Không cho tạo circular reference (A → B → C → A)
- Parent phải tồn tại trước khi assign

### Module Product
**Đặc điểm:**
- Belongs to Category
- Has many ProductVariants
- Specs array (JSON)
- Sluggable

**Files đã tạo:**
- Domain: 2 DTOs, 3 Actions, 1 Interface, 1 Exception
- Infrastructure: 2 Models (Product, ProductVariant), 1 Repository
- App: 1 Controller, 2 Requests

**Logic nghiệp vụ:**
- Category phải tồn tại
- Validate specs array structure
- Support thumbnail upload

## Tham chiếu lý thuyết

Kiến trúc này tuân thủ 100% theo:
- **Layered Architecture** (Domain-Application-Infrastructure)
- **Domain-Driven Design** principles
- **Dependency Inversion Principle** (DIP)
- **Clean Architecture** (Dependency Rule: ngoài → trong)

Đã được xác nhận khớp với sách lý thuyết về Layered Architecture trong DDD.
