<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\Tag;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, Tag $tag): bool
    {
        return $authUser->can('view_any_software_tag');
    }

    public function view(AuthUser $authUser, Tag $tag): bool
    {
        return $authUser->can('view_software_tag');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_tag');
    }

    public function update(AuthUser $authUser, Tag $tag): bool
    {
        return $authUser->can('update_software_tag');
    }

    public function delete(AuthUser $authUser, Tag $tag): bool
    {
        return $authUser->can('delete_software_tag');
    }

    public function deleteAny(AuthUser $authUser, Tag $tag): bool
    {
        return $authUser->can('delete_any_software_tag');
    }
}
