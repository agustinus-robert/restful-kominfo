<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomApiController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'ADMIN') {
            return response()->json([
                'error_code' => 'FORBIDDEN',
                'message' => 'Anda tidak memiliki akses untuk menambahkan ruangan.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code' => 'VALIDATION_ERROR',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $room = Room::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'is_active' => true,
        ]);

        return response()->json([
            'id' => $room->id,
            'name' => $room->name,
            'capacity' => $room->capacity,
            'is_active' => $room->is_active,
        ], 201);
    }

    public function available(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_code' => 'VALIDATION_ERROR',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $rooms = Room::query()
            ->where('is_active', true)
            ->whereDoesntHave('bookings', function ($query) use ($request) {
                $query
                    ->where('status', 'CONFIRMED')
                    ->where('start_time', '<', $request->end)
                    ->where('end_time', '>', $request->start);
            })
            ->select('id', 'name', 'capacity')
            ->get();

        return response()->json([
            'data' => $rooms,
        ], 200);
    }
}