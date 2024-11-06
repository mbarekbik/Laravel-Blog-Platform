<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'excerpt', 'body', 'image_path', 'slug', 'is_published', 'additional_info', 'category_id', 'read_time', 'change_user_id', 'changelog'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function changeUser()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('id', 'DESC');
    }

    public function historypost()
    {
        return $this->hasMany(HistoryPost::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function highlightPosts()
    {
        return $this->hasMany(HighlightPost::class);
    }
}
