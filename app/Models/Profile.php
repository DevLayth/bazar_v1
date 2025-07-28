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

    public function address1()
{
    return $this->belongsTo(Address::class, 'address_1');
}

public function address2()
{
    return $this->belongsTo(Address::class, 'address_2');
}

}
