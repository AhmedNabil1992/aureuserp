<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Website\Models\Page;

class PagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('view_any_website_page');
    }

    public function view(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('view_website_page');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_website_page');
    }

    public function update(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('update_website_page');
    }

    public function delete(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('delete_website_page');
    }

    public function deleteAny(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('delete_any_website_page');
    }

    public function restore(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('restore_website_page');
    }

    public function restoreAny(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('restore_any_website_page');
    }

    public function forceDelete(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('force_delete_website_page');
    }

    public function forceDeleteAny(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('force_delete_any_website_page');
    }
}
