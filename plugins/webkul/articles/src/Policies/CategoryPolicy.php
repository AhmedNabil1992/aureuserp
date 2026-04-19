<?php

namespace Webkul\Article\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Article\Models\Category;
use Webkul\Security\Models\User;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_article_category');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->can('view_article_category');
    }

    public function create(User $user): bool
    {
        return $user->can('create_article_category');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('update_article_category');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can('delete_article_category');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_article_category');
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->can('force_delete_article_category');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_article_category');
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->can('restore_article_category');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_article_category');
    }
}
