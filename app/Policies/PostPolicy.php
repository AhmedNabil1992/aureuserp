<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Blog\Models\Post;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('view_any_blog_post');
    }

    public function view(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('view_blog_post');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_blog_post');
    }

    public function update(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('update_blog_post');
    }

    public function delete(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('delete_blog_post');
    }

    public function deleteAny(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('delete_any_blog_post');
    }

    public function restore(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('restore_blog_post');
    }

    public function restoreAny(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('restore_any_blog_post');
    }

    public function forceDelete(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('force_delete_blog_post');
    }

    public function forceDeleteAny(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('force_delete_any_blog_post');
    }
}
