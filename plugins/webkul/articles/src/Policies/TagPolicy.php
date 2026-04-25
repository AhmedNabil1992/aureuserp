<?php

namespace Webkul\Article\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Article\Models\Tag;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(User|Partner $user): bool
    {
        if ($user instanceof Partner) {
            return true;
        }

        return $user->can('view_any_article_tag');
    }

    public function view(User|Partner $user, Tag $tag): bool
    {
        if ($user instanceof Partner) {
            return true;
        }

        return $user->can('view_article_tag');
    }

    public function create(User|Partner $user): bool
    {
        if ($user instanceof Partner) {
            return false;
        }

        return $user->can('create_article_tag');
    }

    public function update(User|Partner $user, Tag $tag): bool
    {
        if ($user instanceof Partner) {
            return false;
        }

        return $user->can('update_article_tag');
    }

    public function delete(User|Partner $user, Tag $tag): bool
    {
        if ($user instanceof Partner) {
            return false;
        }

        return $user->can('delete_article_tag');
    }

    public function deleteAny(User|Partner $user): bool
    {
        if ($user instanceof Partner) {
            return false;
        }

        return $user->can('delete_any_article_tag');
    }

    public function forceDelete(User|Partner $user, Tag $tag): bool
    {
        if ($user instanceof Partner) {
            return false;
        }

        return $user->can('force_delete_article_tag');
    }

    public function forceDeleteAny(User|Partner $user): bool
    {
        if ($user instanceof Partner) {
            return false;
        }

        return $user->can('force_delete_any_article_tag');
    }

    public function restore(User|Partner $user, Tag $tag): bool
    {
        if ($user instanceof Partner) {
            return false;
        }

        return $user->can('restore_article_tag');
    }

    public function restoreAny(User|Partner $user): bool
    {
        if ($user instanceof Partner) {
            return false;
        }

        return $user->can('restore_any_article_tag');
    }
}
