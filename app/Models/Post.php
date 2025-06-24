<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'price',
        'currency',
        'images',
        'body',
        'pending',
        // ID of the user who approved the post (nullable)
                'approved_by',
    ];

    protected $casts = [
        'images' => 'array',
        'pending' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship to the user who approved the post
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

