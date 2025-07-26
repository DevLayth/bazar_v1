<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'address',
        'phone',
        'phone_otp',
        'img',
        'latitude',
        'longitude',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
