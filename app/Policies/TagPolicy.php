<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tag');
    }

    public function view(User $user, Tag $tag): bool
    {
        return $user->can('view_tag');
    }

    public function create(User $user): bool
    {
        return $user->can('create_tag');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->can('update_tag');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->can('delete_tag');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_tag');
    }

    public function forceDelete(User $user, Tag $tag): bool
    {
        return $user->can('force_delete_tag');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_tag');
    }

    public function restore(User $user, Tag $tag): bool
    {
        return $user->can('restore_tag');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_tag');
    }

    public function replicate(User $user, Tag $tag): bool
    {
        return $user->can('replicate_tag');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_tag');
    }
}
