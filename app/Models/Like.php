<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    // Add this line to fix the error
    protected $fillable = ['user_id', 'post_id'];

    /**
     * Relationship: A like belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A like belongs to a post
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}