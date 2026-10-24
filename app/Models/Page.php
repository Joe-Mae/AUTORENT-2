<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Page de contenu éditable (CGV, mentions légales, confidentialité…).
 * Liée par son slug dans les URL : /fr/pages/mentions-legales.
 */
class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }
}
