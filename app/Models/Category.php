<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, SoftDeletes, Sluggable, HasTranslations;

    public $translatable = ['name', 'description'];
    
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];
    
    public function getAvailableLocales(): array
    {
        return config('app.available_locales', ['vi', 'en']);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true,
            ]
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderByRaw("name->>'vi'");
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public static function tree()
    {
        return static::with('allChildren')
            ->whereNull('parent_id')
            ->orderByRaw("name->>'vi'")
            ->get();
    }

    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_name . ' > ' . $this->name;
        }
        return $this->name;
    }
}
