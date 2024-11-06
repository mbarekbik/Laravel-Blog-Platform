<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id', 'title', 'excerpt', 'body', 'image_path', 'slug', 'is_published', 'additional_info', 'category_id', 'read_time', 'change_user_id', 'changelog', 'created_at', 'updated_at'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->post->user();
    }

    public function changeUser()
    {
        return $this->belongsTo(User::class);
    }

}
