<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
            )->pluck('id');

            if ($roomIds->isEmpty()) {
                throw new RuntimeException(
                    'Ruangan belum tersedia.'
                );
            }

            $admin = User::where('role', 'ADMIN')->firstOrFail();
            $today = Carbon::today('Asia/Jakarta');

            $bookings = [
                [
                    'title' => 'Rapat koordinasi mingguan',
                    'room_id' => $roomIds[0],
                    'start_time' => $today->copy()->setTime(9, 0),
                    'end_time' => $today->copy()->setTime(10, 0),
                    'status' => 'CONFIRMED',
                ],
                [
                    'title' => 'Rapat koordinasi bulanan',
                    'room_id' => $roomIds[1],
                    'start_time' => $today->copy()->setTime(13, 0),
                    'end_time' => $today->copy()->setTime(16, 0),
                    'status' => 'CONFIRMED',
                ],
                [
                    'title' => 'Pembahasan Proyek',
                    'room_id' => $roomIds[2],
                    'start_time' => $today->copy()->setTime(8, 0),
                    'end_time' => $today->copy()->setTime(12, 0),
                    'status' => 'CONFIRMED',
                ],
            ];

            foreach ($bookings as $booking) {
                Booking::withTrashed()->updateOrCreate(
                    ['title' => $booking['title']],
                    [
                        'room_id' => $booking['room_id'],
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
