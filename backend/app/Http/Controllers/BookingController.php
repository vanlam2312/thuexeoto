<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'car_id'     => 'required|exists:cars,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'note'       => 'nullable|string',
        ]);

        // Kiểm tra xe có tồn tại không
        $car = Car::find($request->car_id);

        // Kiểm tra xe có bị trùng lịch không
        $hasBooking = Booking::where('car_id', $request->car_id)
            ->where('status', '!=', 'canceled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhereRaw('? BETWEEN start_date AND end_date', [$request->start_date])
                      ->orWhereRaw('? BETWEEN start_date AND end_date', [$request->end_date]);
            })
            ->exists();

        if ($hasBooking) {
            return response()->json([
                'status' => false,
                'message' => 'Xe đã được thuê trong ngày này. Hãy chọn ngày khác.',
            ], 400);
        }

        // Tính tổng tiền
        $days = (strtotime($request->end_date) - strtotime($request->start_date)) / 86400 + 1;
        $total_price = $car->price * $days;

        // Tạo booking
        $booking = Booking::create([
            'user_id'     => $request->user_id,
            'car_id'      => $request->car_id,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'total_price' => $total_price,
            'status'      => 'pending',
            'note'        => $request->note,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Đặt lịch thành công',
            'data'    => $booking
        ]);
    }
}
