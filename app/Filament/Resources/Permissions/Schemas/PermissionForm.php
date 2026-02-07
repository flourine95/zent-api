<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources.permissions.sections.details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('resources.permissions.fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('e.g., view_any_products, create_products')
                            ->helperText(__('resources.permissions.fields.name_helper')),

                        Textarea::make('description')
                            ->label(__('resources.permissions.fields.description'))
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('e.g., Can view the list of products')
                            ->helperText(__('resources.permissions.fields.description_helper')),

                        TextInput::make('guard_name')
                            ->label(__('resources.permissions.fields.guard_name'))
                            ->required()
                            ->default('web')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(1),

                Section::make(__('resources.permissions.sections.usage'))
                    ->description(__('resources.permissions.sections.usage_description'))
                    ->schema([
                        Placeholder::make('roles_list')
                            ->label(__('resources.permissions.fields.assigned_roles'))
                            ->content(function ($record) {
                                if (! $record || ! $record->exists) {
                                    return __('resources.permissions.messages.no_roles_yet');
                                }

                                $roles = $record->roles;

                                if ($roles->isEmpty()) {
                                    return __('resources.permissions.messages.no_roles_assigned');
                                }

                                return $roles->pluck('name')->join(', ');
                            }),

                        Placeholder::make('users_count')
                            ->label(__('resources.permissions.fields.users_count'))
                            ->content(function ($record) {
                                if (! $record || ! $record->exists) {
                                    return '0';
                                }

                                // Count users through roles
                                $count = $record->roles()
                                    ->withCount('users')
                                    ->get()
                                    ->sum('users_count');

                                return $count.' '.__('resources.permissions.messages.users_via_roles');
                            }),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record && $record->exists),
            ]);
    }
}
