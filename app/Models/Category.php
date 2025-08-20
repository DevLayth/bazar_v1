<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nameEN',
        'nameKU',
        'nameAR',
        'image',
        'parent_id',
        'position',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->with('children');
    }

    public function childrenRecursive()
    {
        return $this
            ->hasMany(Category::class, 'parent_id')
            ->orderBy('position')
            ->with('childrenRecursive');
    }
}
