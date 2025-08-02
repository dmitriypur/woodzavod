<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class House extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['title', 'slug', 'description', 'area_total', 'floor_count', 'brus_volume', 'bedroom_count', 'bathroom_count', 'is_published', 'price', 'old_price', 'seo'];

    protected $casts = [
        'seo' => 'json',
        'is_published' => 'bool',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')->singleFile();
        $this->addMediaCollection('gallery');
    }

    protected static function booted()
    {
        static::creating(function ($house) {
            $house->slug = static::generateUniqueSlug($house->title);
        });

        static::updating(function ($house) {
            // Только если title изменился
            if ($house->isDirty('title')) {
                $house->slug = static::generateUniqueSlug($house->title, $house->id);
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
