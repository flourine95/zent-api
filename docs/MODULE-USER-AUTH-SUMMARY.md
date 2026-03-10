# Module User/Auth - Domain-Oriented Architecture

## Tổng Quan
Module User/Auth quản lý xác thực, phân quyền và thông tin người dùng. Module này tích hợp với Spatie Permission để quản lý roles và permissions.

## Cấu Trúc Thư Mục

### Domain Layer (`app/Domain/User/`)
```
Actions/
├── RegisterUserAction.php          # Đăng ký user mới
├── LoginUserAction.php             # Xác thực đăng nhập
├── UpdateProfileAction.php         # Cập nhật thông tin cá nhân
└── ChangePasswordAction.php        # Đổi mật khẩu

DataTransferObjects/
├── RegisterUserData.php            # DTO cho đăng ký
├── LoginUserData.php               # DTO cho đăng nhập
└── UpdateProfileData.php           # DTO cho cập nhật profile

Repositories/
└── UserRepositoryInterface.php     # Interface định nghĩa contract

Exceptions/
├── UserNotFoundException.php       # User không tồn tại
├── EmailAlreadyExistsException.php # Email đã được sử dụng
└── InvalidCredentialsException.php # Thông tin đăng nhập sai
```

### Infrastructure Layer (`app/Infrastructure/`)
```
Models/
└── User.php                        # Eloquent Model (moved from app/Models/)

Repositories/
└── EloquentUserRepository.php      # Implementation với Eloquent
```

### App Layer (`app/App/User/`)
```
Controllers/
├── AuthController.php              # Xử lý đăng ký, đăng nhập, đăng xuất
└── ProfileController.php           # Xử lý cập nhật profile, đổi mật khẩu

Requests/
├── RegisterRequest.php             # Validation đăng ký
├── LoginRequest.php                # Validation đăng nhập
├── UpdateProfileRequest.php        # Validation cập nhật profile
└── ChangePasswordRequest.php       # Validation đổi mật khẩu
```

## Luồng Dữ Liệu

### 1. Đăng Ký User Mới
```
RegisterRequest (validate)
    ↓
RegisterUserData (DTO)
    ↓
RegisterUserAction (business logic)
    ↓ check email exists
    ↓ create user
UserRepositoryInterface
    ↓
EloquentUserRepository (hash password, save to DB)
    ↓
Return array (user data)
    ↓
AuthController (create token, return response)
```

### 2. Đăng Nhập
```
LoginRequest (validate)
    ↓
LoginUserData (DTO)
    ↓
LoginUserAction (business logic)
    ↓ find by email
    ↓ verify password
UserRepositoryInterface
    ↓
EloquentUserRepository (query DB, check password)
    ↓
Return array (user data)
    ↓
AuthController (create token, return response)
```

### 3. Cập Nhật Profile
```
UpdateProfileRequest (validate)
    ↓
UpdateProfileData (DTO)
    ↓
UpdateProfileAction (business logic)
    ↓ check user exists
    ↓ check email unique (except current user)
    ↓ update user
UserRepositoryInterface
    ↓
EloquentUserRepository (update DB)
    ↓
Return array (updated user data)
    ↓
ProfileController (return response)
```

### 4. Đổi Mật Khẩu
```
ChangePasswordRequest (validate)
    ↓
ChangePasswordAction (business logic)
    ↓ find user
    ↓ verify old password
    ↓ update password
UserRepositoryInterface
    ↓
EloquentUserRepository (hash new password, update DB)
    ↓
Return bool (success)
    ↓
ProfileController (return response)
```

## Repository Interface Methods

```php
interface UserRepositoryInterface
{
    // CRUD Operations
    public function create(array $data): array;
    public function update(int $id, array $data): array;
    public function delete(int $id): bool;
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;
    public function getAll(): array;
    
    // Validation Methods
    public function exists(int $id): bool;
    public function emailExists(string $email): bool;
    public function emailExistsExcept(string $email, int $exceptUserId): bool;
    
    // Password Methods
    public function verifyPassword(string $plainPassword, string $hashedPassword): bool;
    public function updatePassword(int $userId, string $newPassword): bool;
    
    // Token Methods (Sanctum)
    public function createToken(int $userId, string $tokenName): string;
    public function revokeToken(int $userId, string $tokenId): bool;
    public function revokeAllTokens(int $userId): bool;
}
```

## Business Rules

### RegisterUserAction
- Email phải unique trong hệ thống
- Password được hash tự động trong repository
- Throw `EmailAlreadyExistsException` nếu email đã tồn tại

### LoginUserAction
- Kiểm tra user tồn tại qua email
- Verify password với hash trong DB
- Throw `InvalidCredentialsException` nếu sai thông tin

### UpdateProfileAction
- User phải tồn tại
- Email mới phải unique (trừ email hiện tại của user)
- Throw `UserNotFoundException` nếu user không tồn tại
- Throw `EmailAlreadyExistsException` nếu email đã được dùng

### ChangePasswordAction
- User phải tồn tại
- Old password phải đúng
- New password được hash tự động
- Throw `UserNotFoundException` nếu user không tồn tại
- Throw `InvalidCredentialsException` nếu old password sai

## Tích Hợp Spatie Permission

User model sử dụng trait `HasRoles` từ Spatie Permission:
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

Cho phép:
- Gán roles cho user: `$user->assignRole('admin')`
- Kiểm tra permission: `$user->hasPermissionTo('edit articles')`
- Kiểm tra role: `$user->hasRole('admin')`

## Sanctum Token Authentication

Repository cung cấp methods quản lý token:
- `createToken()`: Tạo token mới cho user
- `revokeToken()`: Thu hồi 1 token cụ thể
- `revokeAllTokens()`: Thu hồi tất cả token của user

## API Endpoints

### Authentication
- `POST /api/register` - Đăng ký user mới
- `POST /api/login` - Đăng nhập
- `POST /api/logout` - Đăng xuất (requires auth)
- `GET /api/me` - Lấy thông tin user hiện tại (requires auth)

### Profile Management
- `PUT /api/profile` - Cập nhật thông tin cá nhân (requires auth)
- `PUT /api/profile/password` - Đổi mật khẩu (requires auth)

## Files Updated (Namespace Migration)

Đã cập nhật 15 files để sử dụng namespace mới `App\Infrastructure\Models\User`:

### Seeders (6 files)
- `database/seeders/UserSeeder.php`
- `database/seeders/WishlistSeeder.php`
- `database/seeders/CartSeeder.php`
- `database/seeders/RolePermissionSeeder.php`
- `database/seeders/OrderSeeder.php`

### Factories (1 file)
- `database/factories/UserFactory.php`

### Policies (5 files)
- `app/Policies/UserPolicy.php`
- `app/Policies/CategoryPolicy.php`
- `app/Policies/ProductPolicy.php`
- `app/Policies/OrderPolicy.php`
- `app/Policies/WarehousePolicy.php`

### Providers (1 file)
- `app/Providers/TelescopeServiceProvider.php`

### Filament (2 files)
- `app/Filament/Resources/Users/UserResource.php`
- `app/Filament/Widgets/StatsOverviewWidget.php`

## Service Provider Binding

Đã đăng ký trong `app/Providers/DomainServiceProvider.php`:
```php
UserRepositoryInterface::class => EloquentUserRepository::class
```

## Tuân Thủ Domain-Oriented Design

✅ Domain Layer: Pure PHP, không import Eloquent/Facade
✅ Repository Pattern: Interface trong Domain, Implementation trong Infrastructure
✅ Repository trả về array, không trả Eloquent model
✅ Controller: Validate → DTO → Action → Response
✅ Business logic tập trung trong Action classes
✅ Exceptions đặc thù cho từng nghiệp vụ
✅ Password hashing xử lý trong Infrastructure layer
✅ Token management xử lý trong Infrastructure layer

## Tổng Số Files

- **Domain Layer**: 11 files (4 Actions + 3 DTOs + 1 Interface + 3 Exceptions)
- **Infrastructure Layer**: 2 files (1 Model + 1 Repository)
- **App Layer**: 6 files (2 Controllers + 4 Requests)
- **Total**: 19 files

## Hoàn Thành
✅ Module User/Auth đã được refactor hoàn toàn theo Domain-Oriented Architecture
✅ Tất cả namespace references đã được cập nhật
✅ Composer autoload đã được rebuild
✅ Code đã được format với Laravel Pint
