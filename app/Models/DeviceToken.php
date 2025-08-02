<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DeviceToken extends Model
{
    protected $fillable = [
        'token',
        'device_type',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function deviceTokens()
{
    return $this->morphMany(\App\Models\DeviceToken::class, 'owner');
}
}
