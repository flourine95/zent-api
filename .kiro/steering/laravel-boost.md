---
inclusion: always
---

# Laravel Boost Guidelines

This Laravel application uses specific package versions and conventions. Follow these guidelines for consistent, maintainable code.

## Package Versions

- **PHP**: 8.5.2
- **Laravel**: v12 (streamlined structure)
- **Filament**: v5 (admin panel)
- **Livewire**: v4
- **Pest**: v4 (testing)
- **Tailwind CSS**: v4
- **Laravel Pint**: v1 (code formatting)

## Core Principles

- **Documentation First**: Always use `search-docs` tool before implementing Laravel/ecosystem features
- **Convention Over Configuration**: Follow existing patterns in sibling files
- **Test-Driven**: Write tests for all new functionality using Pest
- **Type Safety**: Use explicit return types and parameter hints
- **Laravel Way**: Prefer framework features over custom solutions

## Required Tool Usage

### Laravel Boost MCP Tools

- `search-docs`: Query version-specific documentation (use before implementation)
- `list-artisan-commands`: Check available Artisan commands and options
- `tinker`: Execute PHP code for debugging/testing
- `database-query`: Read-only database queries
- `get-absolute-url`: Generate correct project URLs
- `browser-logs`: Debug frontend issues

### Documentation Search Patterns

Use multiple, broad queries: `['authentication', 'middleware', 'validation']`
- Simple words: `authentication` (finds auth, authenticate)
- Multiple words: `rate limit` (AND logic)
- Exact phrases: `"infinite scroll"`
- Don't include package names in queries

## PHP Standards

### Type Declarations
```php
// Always use explicit return types and parameter hints
protected function isAccessible(User $user, ?string $path = null): bool
{
    return $user->hasPermission($path);
}
```

### Constructor Property Promotion
```php
// Use PHP 8 constructor promotion
public function __construct(
    public GitHub $github,
    private UserService $userService
) {}
```

### Code Style
- Use curly braces for all control structures
- Prefer PHPDoc blocks over inline comments
- Enum keys should be TitleCase: `FavoritePerson`, `Monthly`
- Run `vendor/bin/pint --dirty --format agent` before finalizing changes

## Laravel 12 Architecture

### File Structure (Streamlined)
- **Middleware**: Register in `bootstrap/app.php` using `withMiddleware()`
- **Providers**: List in `bootstrap/providers.php`
- **Console**: Commands auto-discovered in `app/Console/Commands/`
- **Routes**: Configure in `bootstrap/app.php`

### Database & Models
- Use Eloquent relationships with return type hints
- Prefer `Model::query()` over `DB::`
- Prevent N+1 queries with eager loading
- Model casts: Use `casts()` method over `$casts` property
- Migrations: Include all column attributes when modifying

### Best Practices
- Form Requests for validation (not inline)
- Named routes with `route()` function
- Queued jobs with `ShouldQueue` interface
- Config values: `config('app.name')` not `env('APP_NAME')`
- Use factories in tests, check for custom states

## Filament v5 Patterns

### Component Initialization
```php
// Use static make() methods
TextInput::make('name')
    ->required()
    ->maxLength(255)
```

### Conditional Logic
```php
use Filament\Schemas\Components\Utilities\Get;

Select::make('type')
    ->options(CompanyType::class)
    ->live(),

TextInput::make('company_name')
    ->visible(fn (Get $get): bool => $get('type') === 'business')
```

### Computed Columns
```php
TextColumn::make('full_name')
    ->state(fn (User $record): string => "{$record->first_name} {$record->last_name}")
```

### Actions with Forms
```php
Action::make('updateEmail')
    ->form([
        TextInput::make('email')->email()->required(),
    ])
    ->action(fn (array $data, User $record): void => $record->update($data))
```

### Critical Namespaces
- Form components: `Filament\Forms\Components\`
- Infolist entries: `Filament\Infolists\Components\`
- Layout components: `Filament\Schemas\Components\`
- Schema utilities: `Filament\Schemas\Components\Utilities\`
- Actions: `Filament\Actions\`
- Icons: `Filament\Support\Icons\Heroicon`

### Breaking Changes
- File visibility defaults to `private` (use `->visibility('public')`)
- Grid/Section/Fieldset don't span all columns by default

## Testing with Pest

### Test Creation
```bash
php artisan make:test --pest FeatureName
php artisan test --compact --filter=testName
```

### Filament Testing
```php
// Authenticate first, then test Livewire components
livewire(CreateUser::class)
    ->fillForm(['name' => 'Test', 'email' => 'test@example.com'])
    ->call('create')
    ->assertNotified()
    ->assertRedirect();

// Test validation
livewire(CreateUser::class)
    ->fillForm(['name' => null])
    ->call('create')
    ->assertHasFormErrors(['name' => 'required']);
```

## Development Workflow

### Before Implementation
1. Use `search-docs` for current documentation
2. Check sibling files for existing patterns
3. Verify component reusability
4. Plan test coverage

### Code Quality
- Write descriptive method names: `isRegisteredForDiscounts()` not `discount()`
- Create tests alongside features
- Use factories for test data
- Follow existing naming conventions

### Frontend Issues
If UI changes aren't reflected, user may need to run:
- `npm run build`
- `npm run dev` 
- `composer run dev`

## Restrictions

- Don't create new base directories without approval
- Don't modify dependencies without approval
- Don't create documentation files unless requested
- Don't delete tests without approval
- Prefer tests over verification scripts
