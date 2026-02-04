<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, SoftDeletes, Sluggable, HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'thumbnail', 'specs', 'is_active'];

    protected $casts = [
        'specs' => 'array',
        'is_active' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
