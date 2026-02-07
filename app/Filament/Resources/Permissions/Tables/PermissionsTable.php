<?php

namespace App\Filament\Resources\Permissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resources.permissions.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->formatStateUsing(function ($state) {
                        // Format: view_any_user -> View Any
                        $parts = explode('_', $state);
                        $resource = array_pop($parts);
                        $action = ucwords(str_replace('_', ' ', implode('_', $parts)));

                        return $action;
                    })
                    ->description(fn ($record) => $record->name)
                    ->icon(fn ($record) => self::getActionIcon($record->name)),

                TextColumn::make('resource')
                    ->label(__('resources.permissions.fields.resource'))
                    ->getStateUsing(function ($record) {
                        // Extract resource from permission name
                        $parts = explode('_', $record->name);

                        return ucfirst(end($parts));
                    })
                    ->badge()
                    ->color(fn ($state) => self::getResourceColor($state))
                    ->icon(fn ($state) => self::getResourceIcon($state))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('name', 'like', "%_{$search}");
                    }),

                TextColumn::make('description')
                    ->label(__('resources.permissions.fields.description'))
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description)
                    ->toggleable(),

                TextColumn::make('roles.name')
                    ->label(__('resources.permissions.fields.roles'))
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->limitList(3)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('guard_name')
                    ->label(__('resources.permissions.fields.guard_name'))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('resources.permissions.fields.created_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('resource')
                    ->label(__('resources.permissions.filters.resource'))
                    ->options(self::getResourceOptions())
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->where('name', 'like', "%_{$data['value']}");
                    }),

                SelectFilter::make('guard_name')
                    ->label(__('resources.permissions.filters.guard'))
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Chưa có quyền nào')
            ->emptyStateDescription('Tạo quyền để kiểm soát truy cập hệ thống.')
            ->emptyStateIcon('heroicon-o-lock-closed')
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->defaultSort('name');
    }

    protected static function getResourceOptions(): array
    {
        return [
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
    }

    protected static function getResourceIcon(string $resource): string
    {
        $icons = [
            'User' => 'heroicon-o-users',
            'Role' => 'heroicon-o-user-group',
            'Permission' => 'heroicon-o-lock-closed',
            'Product' => 'heroicon-o-cube',
            'Category' => 'heroicon-o-folder',
            'Order' => 'heroicon-o-shopping-cart',
            'Warehouse' => 'heroicon-o-building-storefront',
            'Tag' => 'heroicon-o-tag',
            'Post' => 'heroicon-o-document-text',
        ];

        return $icons[$resource] ?? 'heroicon-o-shield-check';
    }

    protected static function getResourceColor(string $resource): string
    {
        $colors = [
            'User' => 'info',
            'Role' => 'warning',
            'Permission' => 'danger',
            'Product' => 'success',
            'Category' => 'primary',
            'Order' => 'warning',
            'Warehouse' => 'gray',
            'Tag' => 'info',
            'Post' => 'success',
        ];

        return $colors[$resource] ?? 'gray';
    }

    protected static function getActionIcon(string $permissionName): string
    {
        if (str_contains($permissionName, 'view')) {
            return 'heroicon-o-eye';
        } elseif (str_contains($permissionName, 'create')) {
            return 'heroicon-o-plus-circle';
        } elseif (str_contains($permissionName, 'update')) {
            return 'heroicon-o-pencil';
        } elseif (str_contains($permissionName, 'delete')) {
            return 'heroicon-o-trash';
        } elseif (str_contains($permissionName, 'restore')) {
            return 'heroicon-o-arrow-path';
        } elseif (str_contains($permissionName, 'replicate')) {
            return 'heroicon-o-document-duplicate';
        } elseif (str_contains($permissionName, 'reorder')) {
            return 'heroicon-o-arrows-up-down';
        }

        return 'heroicon-o-shield-check';
    }
}
