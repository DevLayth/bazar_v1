<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlanSubscription extends Model
{
    use HasFactory;
    protected $table = 'user_plan_subscription';
    protected $fillable = [
        'user_id',
        'plan_id',
        'posts_counter',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
