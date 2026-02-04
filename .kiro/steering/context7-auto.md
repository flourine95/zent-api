---
inclusion: always
---

# Context7 Documentation Integration

## Required Libraries

When working with this codebase, proactively use Context7 MCP tools to retrieve current documentation for these core dependencies:

### Framework & Admin Panel
- **Laravel**: `/laravel/laravel` - Core framework patterns, Eloquent ORM, routing, middleware, validation
- **Filament PHP v5**: `/websites/filamentphp_5_x` - Admin panel resources, forms, tables, actions, relation managers

### Spatie Packages
- **Laravel Permission**: `/websites/spatie_be_laravel-permission_v6` - Roles, permissions, guards
- **Laravel Translatable**: `/spatie/laravel-translatable` - Model translations, locale handling
- **Translation Loader**: `/spatie/laravel-translation-loader` - Database-driven translations

## Usage Guidelines

1. **Automatic Invocation**: Query Context7 automatically when questions involve these libraries - no explicit "use context7" request needed
2. **Resolve First**: Always call `resolve-library-id` before `query-docs` to get the correct library identifier
3. **Specific Queries**: Frame queries with specific implementation details (e.g., "How to define custom Filament table filters" vs "Filament tables")
4. **Version Awareness**: Use the exact library paths above to ensure version-specific documentation

## When to Query

- Implementing new Filament resources, forms, or table configurations
- Working with Eloquent relationships or model scopes
- Setting up permissions, roles, or authorization logic
- Adding translatable fields to models
- Troubleshooting framework-specific errors or deprecations
