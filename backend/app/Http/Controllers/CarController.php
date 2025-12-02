<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    // ============================
    //       GET LIST OF CARS
    // ============================
    public function index()
    {
        $cars = Car::all();

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách xe thành công',
            'data' => $cars
        ]);
    }
    // ============================
    //       SEARCH CARS
    // ============================
    public function search(Request $request)
    {
        $query = Car::query();

        // Search theo tên xe
        if ($request->filled('keyword')) {
            $query->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        // Lọc theo hãng xe
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Lọc theo số chỗ ngồi
        if ($request->filled('seats')) {
            $query->where('seats', $request->seats);
        }

        // Lấy tất cả xe khớp điều kiện
        $cars = $query->get();

        return response()->json([
            'success' => true,
            'data' => $cars
        ]);
    }
    // ============================
    //       GET CAR DETAILS
    // ============================
    public function show($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
            'message' => 'Không tìm thấy xe'
        ], 404);
        }

        return response()->json([
           'message' => 'Lấy chi tiết xe thành công',
            'data' => $car
        ]);
    }
    // ============================
    //       ADD NEW CAR
    // ============================
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'brand'          => 'required|string|max:255',
            'price_per_day'  => 'required|numeric',
            'seats'          => 'required|integer',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        // Upload ảnh nếu có
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('cars'), $imageName);
        }

        $car = Car::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'price_per_day' => $request->price_per_day,
            'seats' => $request->seats,
            'description' => $request->description,
            'image' => $imageName
        ]);
        return response()->json([
            'message' => 'Thêm xe thành công',
            'data' => $car
        ], 201);
    }
    // ============================
    //       UPDATE CAR
    // ============================
    public function update(Request $request, $id)
{
    $car = Car::find($id);

    if (!$car) {
        return response()->json([
            'message' => 'Không tìm thấy xe'
        ], 404);
    }

    $request->validate([
        'name'           => 'required|string|max:255',
        'brand'          => 'required|string|max:255',
        'price_per_day'  => 'required|numeric',
        'seats'          => 'required|integer',
        'description'    => 'nullable|string',
        'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
    ]);

    // Nếu có ảnh mới → upload ảnh mới
    if ($request->hasFile('image')) {

        // Xóa ảnh cũ nếu có
        if ($car->image && file_exists(public_path('cars/' . $car->image))) {
            unlink(public_path('cars/' . $car->image));
        }

        // Upload ảnh mới
        $imageName = time() . '_' . $request->image->getClientOriginalName();
        $request->image->move(public_path('cars'), $imageName);

        $car->image = $imageName;
    }

    // Cập nhật dữ liệu
    $car->update([
        'name'          => $request->name,
        'brand'         => $request->brand,
        'price_per_day' => $request->price_per_day,
        'seats'         => $request->seats,
        'description'   => $request->description,
    ]);

    return response()->json([
        'message' => 'Cập nhật xe thành công',
        'data' => $car
    ]);
}
    // ============================
    //       DELETE CAR
    // ============================
    public function destroy($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'message' => 'Không tìm thấy xe'
            ], 404);
        }

        // Xóa ảnh nếu có
        if ($car->image && file_exists(public_path('cars/' . $car->image))) {
            unlink(public_path('cars/' . $car->image));
        }

        $car->delete();

        return response()->json([
            'message' => 'Xóa xe thành công'
        ]);
    }
}
