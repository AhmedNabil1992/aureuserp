<?php

namespace Webkul\Article\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Article\Models\Article;
use Webkul\Security\Models\User;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_article_article');
    }

    public function view(User $user, Article $article): bool
    {
        return $user->can('view_article_article');
    }

    public function create(User $user): bool
    {
        return $user->can('create_article_article');
    }

    public function update(User $user, Article $article): bool
    {
        return $user->can('update_article_article');
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->can('delete_article_article');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_article_article');
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return $user->can('force_delete_article_article');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_article_article');
    }

    public function restore(User $user, Article $article): bool
    {
        return $user->can('restore_article_article');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_article_article');
    }
}
