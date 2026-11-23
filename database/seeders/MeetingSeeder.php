<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $rooms = [
                [
                    'name' => 'Ruang Meeting 1',
                    'capacity' => 10,
                    'is_active' => true,
                ],
                [
                    'name' => 'Ruang Meeting 2',
                    'capacity' => 20,
                    'is_active' => true,
                ],
                [
                    'name' => 'Ruang Meeting 3',
                    'capacity' => 30,
                    'is_active' => true,
                ],
                [
                    'name' => 'Ruang Meeting 4',
                    'capacity' => 50,
                    'is_active' => false,
                ],
                [
                    'name' => 'Ruang Koordinasi Meeting',
                    'capacity' => 30,
                    'is_active' => false,
                ],
            ];

            foreach ($rooms as $room) {
                Room::withTrashed()->updateOrCreate(
                    ['name' => $room['name']],
                    array_merge($room, [
                        'deleted_at' => null,
                    ])
                );
            }

            $roomIds = Room::whereIn(
                'name',
                array_column($rooms, 'name')
            )
            ->where('is_active', true)
            ->pluck('id');

            $userIds = User::pluck('id');

            if ($userIds->isEmpty()) {
                throw new RuntimeException(
                    'Users belum tersedia. Jalankan UserSeeder terlebih dahulu.'
                );
            }

            $bookings = [
                [
                    'title' => 'Rapat koordinasi mingguan',
                    'start_time' => '2026-11-24T02:00:00Z',
                    'end_time' => '2026-11-24T03:00:00Z',
                    'status' => 'CONFIRMED',
                ],
                [
                    'title' => 'Rapat koordinasi bulanan',
                    'start_time' => '2026-11-25T02:00:00Z',
                    'end_time' => '2026-11-25T04:00:00Z',
                    'status' => 'CONFIRMED',
                ],
                [
                    'title' => 'Pembahasan Proyek',
                    'start_time' => '2026-11-24T04:00:00Z',
                    'end_time' => '2026-11-24T05:30:00Z',
                    'status' => 'CANCELLED',
                ],
            ];

            if ($roomIds->isEmpty()) {
                throw new RuntimeException(
                    'Tidak ada ruangan aktif untuk booking.'
                );
            }

            $admin = User::where('role', 'ADMIN')->firstOrFail();
            foreach ($bookings as $booking) {
                Booking::withTrashed()->updateOrCreate(
                    ['title' => $booking['title']],
                    [
                        'room_id' => $roomIds->random(),
                        'user_id' => $admin->id,
                        'start_time' => $booking['start_time'],
                        'end_time' => $booking['end_time'],
                        'status' => $booking['status'],
                        'deleted_at' => null,
                    ]
                );
            }
        });
    }
}
