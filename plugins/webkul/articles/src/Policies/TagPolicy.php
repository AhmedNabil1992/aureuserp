<?php

namespace Webkul\Article\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Article\Models\Tag;
use Webkul\Security\Models\User;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_article_tag');
    }

    public function view(User $user, Tag $tag): bool
    {
        return $user->can('view_article_tag');
    }

    public function create(User $user): bool
    {
        return $user->can('create_article_tag');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->can('update_article_tag');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->can('delete_article_tag');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_article_tag');
    }

    public function forceDelete(User $user, Tag $tag): bool
    {
        return $user->can('force_delete_article_tag');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_article_tag');
    }

    public function restore(User $user, Tag $tag): bool
    {
        return $user->can('restore_article_tag');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_article_tag');
    }
}
