<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'max_posts_per_month',
        'duration',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
