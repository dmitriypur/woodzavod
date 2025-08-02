<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'title', 'content', 'meta_title', 'meta_description', 'is_published', 'seo'];

    protected $casts = [
        'seo' => 'json',
        'is_published' => 'bool',
    ];

    protected static function booted()
    {
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = static::generateUniqueSlug($page->title);
            }
        });

        static::updating(function ($page) {
            // Только если title изменился и slug пустой
            if ($page->isDirty('title') && empty($page->slug)) {
                $page->slug = static::generateUniqueSlug($page->title, $page->id);
            }
        });
    }

    public static function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $i = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        return $slug;
    }
}
