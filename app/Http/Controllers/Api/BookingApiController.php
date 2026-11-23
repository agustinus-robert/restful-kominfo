<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BookingApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code' => 'VALIDATION_ERROR',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);

            if (
                $endTime->lessThanOrEqualTo($startTime) ||
                $startTime->lessThanOrEqualTo(Carbon::now())
            ) {
                return response()->json([
                    'error_code' => 'INVALID_TIME',
                    'message' => 'Waktu pemesanan tidak valid atau sudah berlalu.',
                ], 400);
            }

            $booking = DB::transaction(function () use (
                $request,
                $startTime,
                $endTime
            ) {
                $room = Room::where('id', $request->room_id)
                    ->lockForUpdate()
                    ->first();

                if (!$room) {
                    return [
                        'error' => true,
                        'error_code' => 'ROOM_NOT_FOUND',
                        'message' => 'Ruangan tidak ditemukan.',
                        'status' => 404,
                    ];
                }

                if (!$room->is_active) {
                    return [
                        'error' => true,
                        'error_code' => 'ROOM_INACTIVE',
                        'message' => 'Ruangan sedang tidak aktif.',
                        'status' => 400,
                    ];
                }

                $conflict = Booking::where('room_id', $room->id)
                    ->where('status', 'CONFIRMED')
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                    ->exists();

                if ($conflict) {
                    return [
                        'error' => true,
                        'error_code' => 'ROOM_CONFLICT',
                        'message' => 'Ruangan sudah dipesan pada waktu tersebut.',
                        'status' => 409,
                    ];
                }

                return Booking::create([
                    'room_id' => $room->id,
                    'user_id' => $request->user()->id,
                    'title' => $request->title,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'CONFIRMED',
                ]);
            }, 5);

            if (is_array($booking) && ($booking['error'] ?? false)) {
                return response()->json([
                    'error_code' => $booking['error_code'],
                    'message' => $booking['message'],
                ], $booking['status']);
            }

            return response()->json([
                'id' => $booking->id,
                'room_id' => $booking->room_id,
                'user_id' => $booking->user_id,
                'title' => $booking->title,
                'start_time' => $booking->start_time->toISOString(),
                'end_time' => $booking->end_time->toISOString(),
                'status' => $booking->status,
            ], 201);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'error_code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Terjadi kesalahan saat membuat pemesanan.',
            ], 500);
        }
    }
}