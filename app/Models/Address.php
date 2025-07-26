<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['name_en', 'name_ku', 'name_ar', 'parent_id'];

    public function children()
    {
        return $this->hasMany(Address::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Address::class, 'parent_id');
    }
}
