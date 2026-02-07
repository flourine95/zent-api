<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources.roles.sections.details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('resources.roles.fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('e.g., Super Admin, Manager, Staff')
                            ->helperText(__('resources.roles.fields.name_helper')),

                        TextInput::make('guard_name')
                            ->label(__('resources.roles.fields.guard_name'))
                            ->required()
                            ->default('web')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Section::make(__('resources.roles.sections.permissions'))
                    ->description(__('resources.roles.sections.permissions_description'))
                    ->schema(self::getPermissionSections())
                    ->columns(1),
            ]);
    }

    protected static function getPermissionSections(): array
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            // Extract resource name from permission (e.g., 'view_any_user' -> 'user')
            $parts = explode('_', $permission->name);

            return end($parts);
        });

        $sections = [];

        foreach ($permissions as $resource => $perms) {
            $sections[] = Section::make(self::getResourceLabel($resource))
                ->description(self::getResourceDescription($resource))
                ->icon(self::getResourceIcon($resource))
                ->collapsible()
                ->collapsed()
                ->schema([
                    CheckboxList::make('permissions')
                        ->label(false)
                        ->relationship('permissions', 'name')
                        ->options(
                            $perms->mapWithKeys(function ($permission) {
                                return [$permission->id => self::formatPermissionLabel($permission->name)];
                            })
                        )
                        ->descriptions(
                            $perms->mapWithKeys(function ($permission) {
                                // Use description from database if available
                                return [$permission->id => $permission->description ?? ''];
                            })
                        )
                        ->columns(3)
                        ->gridDirection('row')
                        ->bulkToggleable(),
                ]);
        }

        return $sections;
    }

    protected static function getResourceLabel(string $resource): string
    {
        $labels = [
            'user' => __('resources.users.plural_label'),
            'role' => __('resources.roles.plural_label'),
            'permission' => __('resources.permissions.plural_label'),
            'product' => __('resources.products.plural_label'),
            'category' => __('resources.categories.plural_label'),
            'order' => __('resources.orders.plural_label'),
            'warehouse' => __('resources.warehouses.plural_label'),
            'tag' => __('resources.tags.plural_label'),
            'post' => __('resources.posts.plural_label'),
        ];

        return $labels[$resource] ?? ucfirst($resource);
    }

    protected static function getResourceDescription(string $resource): string
    {
        return 'Manage permissions for '.$resource.' module';
    }

    protected static function getResourceIcon(string $resource): string
    {
        $icons = [
            'user' => 'heroicon-o-users',
            'role' => 'heroicon-o-user-group',
            'permission' => 'heroicon-o-lock-closed',
            'product' => 'heroicon-o-cube',
            'category' => 'heroicon-o-folder',
            'order' => 'heroicon-o-shopping-cart',
            'warehouse' => 'heroicon-o-building-storefront',
            'tag' => 'heroicon-o-tag',
            'post' => 'heroicon-o-document-text',
        ];

        return $icons[$resource] ?? 'heroicon-o-shield-check';
    }

    protected static function formatPermissionLabel(string $permission): string
    {
        // Convert 'view_any_user' to 'View Any'
        $parts = explode('_', $permission);
        array_pop($parts); // Remove resource name

        return ucwords(str_replace('_', ' ', implode('_', $parts)));
    }
}
