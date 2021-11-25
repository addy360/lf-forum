<?php

namespace App\Helpers;

use App\Models\Post;

class CheckPostOwner
{
    public static function is_owner(Post $post, $user)
    {

        return $post->user_id == $user->id;
    }
}
