<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'category_name',
        'category_description',
        'parent_category_id',
        'is_active',
    ];

    // A category can have subcategories (self-referencing)
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }

    // A category belongs to a parent (self-referencing)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    // A category has many complaints
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}