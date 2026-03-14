<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendList extends Model
{
    protected $fillable = ["friend_id", "user_id"];


    public function friend()
    {
        return $this->belongsTo(User::class, "friend_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");

    }
}
