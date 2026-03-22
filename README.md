# Zent API

REST API cho nền tảng thương mại điện tử, xây dựng trên Laravel 12 với kiến trúc Domain-Oriented.

## Yêu cầu

- PHP 8.4+
- PostgreSQL 14+
- Redis (Docker hoặc local)
- Composer
- Node.js (chỉ cần nếu build frontend/admin)

## Cài đặt

### 1. Clone và cài dependencies

```bash
git clone <repo-url>
cd zent-api
composer install
```

### 2. Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Mở `.env` và cấu hình các giá trị sau:

**Database (PostgreSQL):**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=zent_api
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

**Redis:**
```env
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

**Queue & Cache:**
```env
QUEUE_CONNECTION=database
CACHE_STORE=database
```

**Mail (dùng `log` để test local):**
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Zent"
```

**Shipping providers (optional):**
```env
GHTK_API_TOKEN=your_token
GHN_TOKEN=your_token
GHN_SHOP_ID=your_shop_id
```

### 3. Khởi động Redis

```bash
docker run -d --name zent-redis -p 6379:6379 --restart unless-stopped \
  redis:alpine redis-server --appendonly yes
```

Lần sau nếu container bị stop:
```bash
docker start zent-redis
```

### 4. Migrate và seed database

```bash
php artisan migrate --seed
```

Hoặc reset hoàn toàn:
```bash
php artisan migrate:fresh --seed
```

Seed tạo sẵn các tài khoản mặc định:

| Email | Password | Role |
|---|---|---|
| admin@example.com | password | admin |
| customer@example.com | password | customer |

## Chạy dự án

Cần mở **3 terminal** chạy song song:

**Terminal 1 — API server:**
```bash
php artisan serve
```

**Terminal 2 — Queue worker** (xử lý order persistence, email, notifications):
```bash
php artisan queue:work --tries=3
```

**Terminal 3 — Scheduler** (auto-cancel đơn hết hạn, release reservations):
```bash
php artisan schedule:work
```

API base URL: `http://localhost:8000/api/v1`

## Kiến trúc

Dự án dùng Domain-Oriented Architecture với 3 layer:

```
app/App/            → HTTP layer (Controllers, FormRequests)
app/Domain/         → Business logic (Actions, DTOs, Exceptions, Repository Interfaces)
app/Infrastructure/ → Database & external services (Eloquent Models, Repositories, Jobs)
```

Chi tiết kỹ thuật và các quyết định thiết kế xem tại [`docs/roadmap.md`](docs/roadmap.md).

## Các lệnh hữu ích

```bash
# Xem tất cả routes
php artisan route:list

# Chạy tests
php artisan test --compact

# Format code
vendor/bin/pint

# Xem failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```
