---
inclusion: always
---

# Domain-Oriented Architecture

This project uses a custom Domain-Oriented Design adapted for Laravel (not traditional DDD). Layer boundaries are strictly enforced — any violation between Domain and Infrastructure is a critical error.

## Layer Overview

```
app/App/           → HTTP delivery layer (Controllers, FormRequests)
app/Domain/        → Core business logic (framework-agnostic)
app/Infrastructure/ → Database & external services (Eloquent Models, Repositories)
```

## Data Flow (one-way, mandatory)

```
Request → Controller → FormRequest → DTO → Action → Repository Interface
       → Repository Implementation → Eloquent Model → array → Action → Controller → Response
```

---

## App Layer (`app/App/`)

Controllers must do exactly these 4 things, in order:

1. Validate input via a `FormRequest`
2. Map validated data into a DTO
3. Pass the DTO to a Domain `Action`
4. Return the response

**Forbidden in Controllers:**
- Database queries (`Model::where(...)`, `DB::`, etc.)
- Business logic or calculations

---

## Domain Layer (`app/Domain/`)

Pure PHP — framework agnostic. Contains only:

- `Actions` — business logic (conditionals, orchestration)
- `DataTransferObjects` — typed data carriers
- `Exceptions` — domain-specific exceptions
- `Repositories` — interfaces only (no implementations)

**Forbidden in Domain:**
- `Illuminate\Database\Eloquent\Model` or any Eloquent/DB imports
- Laravel Facades (`DB::`, `Cache::`, `Session::`, etc.)

---

## Infrastructure Layer (`app/Infrastructure/`)

The only layer allowed to interact with the database or external services.

- `app/Infrastructure/Models/` — the **only** place Eloquent Models may live
- Repository implementations must implement interfaces defined in `app/Domain/`

**Critical rule:** Repository implementations must **never** return Eloquent Models or Collections to the Domain layer. Always convert to primitives (`array`, `int`, `bool`, `null`) before returning — use `$model->toArray()`.

---

## Examples

**Correct — Controller:**
```php
public function store(CreateProductRequest $request, CreateProductAction $action): JsonResponse
{
    $data = CreateProductData::fromRequest($request);
    $result = $action->execute($data);
    return response()->json($result, 201);
}
```

**Correct — Repository returning array:**
```php
public function findById(int $id): ?array
{
    return ProductModel::query()->find($id)?->toArray();
}
```

**Wrong — returning Eloquent Model from repository:**
```php
// NEVER do this
public function findById(int $id): ?ProductModel
{
    return ProductModel::query()->find($id); // violates layer boundary
}
```
