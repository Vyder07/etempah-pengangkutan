<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
            new PrivateChannel('user.' . $this->booking->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'booking.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->booking->id,
            'user' => [
                'id' => $this->booking->user->id,
                'name' => $this->booking->user->name,
                'email' => $this->booking->user->email,
            ],
            'vehicle_type' => $this->booking->vehicle_type,
            'vehicle_name' => $this->booking->vehicle_name,
            'vehicle_plate' => $this->booking->vehicle_plate,
            'destination' => $this->booking->destination,
            'purpose' => $this->booking->purpose,
            'start_date' => $this->booking->start_date->toIso8601String(),
            'end_date' => $this->booking->end_date->toIso8601String(),
            'status' => $this->booking->status,
            'created_at' => $this->booking->created_at->toIso8601String(),
            'message' => "Tempahan baharu daripada {$this->booking->user->name}",
        ];
    }
}
