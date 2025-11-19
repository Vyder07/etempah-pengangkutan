<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Booking extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_name',
        'vehicle_plate',
        'purpose',
        'destination',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('public');
    }

    /**
     * Get the user who created the booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-500',
            'approved' => 'bg-green-500',
            'rejected' => 'bg-red-500',
            'completed' => 'bg-blue-500',
            'cancelled' => 'bg-gray-500',
            default => 'bg-gray-400',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Diluluskan',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatal',
            default => 'Tidak Diketahui',
        };
    }
}
