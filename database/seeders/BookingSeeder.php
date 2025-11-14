<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user (assuming at least one user exists)
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Please create a user first.');
            return;
        }

        // Clear existing bookings
        Booking::truncate();

        $vehicles = [
            ['name' => 'Proton Saga', 'plate' => 'ABC 1234'],
            ['name' => 'Perodua Myvi', 'plate' => 'DEF 5678'],
            ['name' => 'Toyota Hilux', 'plate' => 'GHI 9012'],
            ['name' => 'Honda City', 'plate' => 'JKL 3456'],
            ['name' => 'Nissan Navara', 'plate' => 'MNO 7890'],
        ];

        $destinations = [
            'Putrajaya',
            'Kuala Lumpur',
            'Cyberjaya',
            'Shah Alam',
            'Petaling Jaya',
            'Klang',
            'Seremban',
            'Nilai',
        ];

        $purposes = [
            'Mesyuarat dengan jabatan lain',
            'Menghantar dokumen penting',
            'Program latihan kakitangan',
            'Lawatan sambil belajar',
            'Menghadiri seminar',
            'Pemeriksaan tapak projek',
            'Mengambil peralatan pejabat',
            'Menghantar surat rasmi',
        ];

        $statuses = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];

        // Create bookings for current month
        $currentMonth = Carbon::now();

        // Past bookings (completed)
        for ($i = 1; $i <= 5; $i++) {
            $startDate = $currentMonth->copy()->subDays(rand(15, 25))->setHour(rand(8, 14))->setMinute([0, 15, 30, 45][rand(0, 3)]);
            $endDate = $startDate->copy()->addHours(rand(2, 8));

            $vehicle = $vehicles[array_rand($vehicles)];

            Booking::create([
                'user_id' => $user->id,
                'vehicle_name' => $vehicle['name'],
                'vehicle_plate' => $vehicle['plate'],
                'purpose' => $purposes[array_rand($purposes)],
                'destination' => $destinations[array_rand($destinations)],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'completed',
                'notes' => 'Tempahan telah selesai dengan jayanya.',
            ]);
        }

        // Current/future bookings with various statuses
        for ($i = 1; $i <= 15; $i++) {
            $startDate = $currentMonth->copy()->addDays(rand(-5, 20))->setHour(rand(8, 16))->setMinute([0, 15, 30, 45][rand(0, 3)]);
            $endDate = $startDate->copy()->addHours(rand(2, 10));

            $vehicle = $vehicles[array_rand($vehicles)];
            $status = $statuses[array_rand($statuses)];

            $notes = match($status) {
                'approved' => 'Tempahan telah diluluskan. Sila ambil kunci kenderaan di kaunter.',
                'rejected' => 'Tempahan ditolak kerana kenderaan tidak tersedia pada tarikh tersebut.',
                'cancelled' => 'Tempahan dibatalkan oleh pemohon.',
                'pending' => null,
                'completed' => 'Tempahan selesai. Kenderaan telah dipulangkan.',
                default => null,
            };

            Booking::create([
                'user_id' => $user->id,
                'vehicle_name' => $vehicle['name'],
                'vehicle_plate' => $vehicle['plate'],
                'purpose' => $purposes[array_rand($purposes)],
                'destination' => $destinations[array_rand($destinations)],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'notes' => $notes,
            ]);
        }

        // Add some bookings for next month
        $nextMonth = Carbon::now()->addMonth();

        for ($i = 1; $i <= 8; $i++) {
            $startDate = $nextMonth->copy()->addDays(rand(1, 25))->setHour(rand(8, 16))->setMinute([0, 15, 30, 45][rand(0, 3)]);
            $endDate = $startDate->copy()->addHours(rand(2, 8));

            $vehicle = $vehicles[array_rand($vehicles)];
            $status = ['pending', 'approved'][rand(0, 1)];

            Booking::create([
                'user_id' => $user->id,
                'vehicle_name' => $vehicle['name'],
                'vehicle_plate' => $vehicle['plate'],
                'purpose' => $purposes[array_rand($purposes)],
                'destination' => $destinations[array_rand($destinations)],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'notes' => $status === 'approved' ? 'Tempahan telah diluluskan untuk bulan hadapan.' : null,
            ]);
        }

        // Add some multi-day bookings
        for ($i = 1; $i <= 3; $i++) {
            $startDate = $currentMonth->copy()->addDays(rand(5, 15))->setHour(8)->setMinute(0);
            $endDate = $startDate->copy()->addDays(rand(2, 5))->setHour(17)->setMinute(0);

            $vehicle = $vehicles[array_rand($vehicles)];

            Booking::create([
                'user_id' => $user->id,
                'vehicle_name' => $vehicle['name'],
                'vehicle_plate' => $vehicle['plate'],
                'purpose' => 'Program outreach jangka panjang',
                'destination' => ['Johor Bahru', 'Penang', 'Ipoh'][rand(0, 2)],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => ['pending', 'approved'][rand(0, 1)],
                'notes' => 'Tempahan untuk program jangka panjang.',
            ]);
        }

        $this->command->info('BookingSeeder completed successfully!');
        $this->command->info('Total bookings created: ' . Booking::count());
    }
}
