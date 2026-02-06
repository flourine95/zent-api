<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, Sluggable, SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'thumbnail', 'specs', 'is_active'];

    protected $casts = [
        'specs' => 'array',
        'is_active' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
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

    /**
     * Render rich content description for display
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if (empty($this->description)) {
            return '';
        }

        // Simple HTML sanitization for security
        return strip_tags($this->description, '<p><br><strong><em><ul><ol><li><a><h1><h2><h3><h4><h5><h6>');
    }
}
