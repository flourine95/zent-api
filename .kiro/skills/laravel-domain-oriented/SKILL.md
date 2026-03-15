---
name: laravel-domain-oriented
description: "Generates code using a custom Domain-Oriented Architecture for Laravel. Activates when the user asks to create a new module, refactor to DDD, generate Actions, DTOs, or Repositories; or when user mentions domain-oriented, layered architecture, module generation, or strict infrastructure boundaries."
license: MIT
metadata:
  author: custom-laravel-architect
---

# Laravel Domain-Oriented Architecture

## When to Apply
Activate this skill when:
- Creating a new module (e.g., Product, Category, Order).
- Refactoring existing Laravel MVC code into Domain/Infrastructure layers.
- Creating Actions, DTOs, or Repositories.

## STRICT ARCHITECTURE RULES (OVERRIDE STANDARD DDD)
This is a **custom, pragmatic version of DDD for Laravel**. DO NOT use standard DDD patterns (like returning Domain Entities from Repositories or creating strict Value Objects) unless specified. You MUST follow these exact boundaries:

### 1. Domain Layer (`app/Domain/{Module}/`)
- Pure PHP only. NO framework dependencies.
- **NEVER** import `Illuminate\Database\Eloquent\Model` or use Facades (`DB::`, `Cache::`).
- Contains ONLY: `DataTransferObjects` (DTOs), `Actions` (business logic), `Repositories` (INTERFACES ONLY), and `Exceptions`.

### 2. Infrastructure Layer (`app/Infrastructure/`)
- The ONLY place for Eloquent Models (`app/Infrastructure/Models/`).
- Contains Repository Implementations (`app/Infrastructure/Repositories/`).
- **CRITICAL RULE:** Repository implementations MUST return primitive types (e.g., `array`, `int`, `bool`). **NEVER** return Eloquent Models or Collections to the Domain layer. Use `$model->toArray()`.

### 3. App Layer (`app/App/{Module}/`)
- Contains `Controllers` and Form `Requests`.
- **CRITICAL RULE:** Controllers only do 4 things: Validate FormRequest -> Create DTO -> Call Action -> Return Response. NO business logic or database queries here.

## Step-by-Step Module Generation

When asked to create a module, strictly follow this flow:

### Step 1: Domain Setup
1. Create DTOs (`Create{Module}Data`, etc.) using `readonly class`.
2. Create Repository Interface (`{Module}RepositoryInterface`).
3. Create Actions (`Create{Module}Action`, etc.) and inject the Repository Interface.
4. Create specific Exceptions.

```php
final readonly class CreateCategoryAction
{
    public function __construct(private CategoryRepositoryInterface $repository) {}

    public function execute(CreateCategoryData $data): array
    {
        return $this->repository->create($data->toArray());
    }
}

```

### Step 2: Infrastructure Setup

1. Create Eloquent Model in `app/Infrastructure/Models/`.
2. Create Repository Implementation.

```php
final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function create(array $data): array
    {
        $model = Category::create($data);
        return $model->toArray(); // Must return array!
    }
}

```

### Step 3: App Layer Setup

1. Create FormRequests.
2. Create Controller.

### Step 4: Binding

* Provide the snippet to bind the Interface to the Implementation in `DomainServiceProvider`.

## Common Pitfalls (DO NOT DO THESE)

* Returning Eloquent Models from Repositories (Always return `array`).
* Writing business logic (if/else rules) inside Controllers.
* Forgetting to inject the Repository Interface into Actions.
* Using `$model->save()` inside an Action (Actions must call the Repository).
