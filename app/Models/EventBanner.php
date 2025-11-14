<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EventBanner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner')
            ->singleFile() // Only one image per banner
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(368)
                    ->height(232)
                    ->sharpen(10);

                $this->addMediaConversion('banner')
                    ->width(1280)
                    ->height(720)
                    ->sharpen(10);
            });
    }

    /**
     * Get active banners ordered by order column
     */
    public static function getActiveBanners()
    {
        return self::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get banner image URL
     */
    public function getBannerUrlAttribute()
    {
        return $this->getFirstMediaUrl('banner', 'banner') ?: asset('IMG/background.png');
    }

    /**
     * Get banner thumbnail URL
     */
    public function getThumbUrlAttribute()
    {
        return $this->getFirstMediaUrl('banner', 'thumb') ?: asset('IMG/background.png');
    }
}
