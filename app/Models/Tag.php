<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    use HasTranslations;

    public $translatable = ['name'];

    protected $fillable = ['name'];
    
    public function getAvailableLocales(): array
    {
        return config('app.available_locales', ['vi', 'en']);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
