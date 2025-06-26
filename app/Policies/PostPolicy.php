<?php
namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    public function modify(User $user, Post $post): Response
    {
        return $user->id === $post->user_id || $user->admin == 1
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }

    public function view(User $user, Post $post): Response
    {
        return $user->id === $post->user_id
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }

    public function delete(User $user, Post $post): Response
    {
        return $user->id === $post->user_id || $user->admin == 1
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }


    public function reject(User $user): Response
    {
        return $user->admin == 1
            ? Response::allow()
            : Response::deny('Only admins can reject posts.');
    }

    public function approve(User $user): Response
    {
        return $user->admin == 1
            ? Response::allow()
            : Response::deny('Only admins can approve posts.');
    }
}
