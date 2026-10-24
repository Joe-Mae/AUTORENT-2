<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Page de contenu éditable (CGV, mentions légales, confidentialité…).
 */
class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content'];

    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }
}
