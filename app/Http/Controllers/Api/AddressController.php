<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Lấy danh sách địa chỉ
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => AddressResource::collection($addresses),
        ]);
    }

    /**
     * Tạo địa chỉ mới
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:2',
            'is_default' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // If this is set as default, unset other defaults
            if ($request->boolean('is_default')) {
                $request->user()->addresses()->update(['is_default' => false]);
            }

            $address = $request->user()->addresses()->create($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm địa chỉ mới',
                'data' => new AddressResource($address),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm địa chỉ',
            ], 500);
        }
    }

    /**
     * Cập nhật địa chỉ
     */
    public function update(Request $request, Address $address): JsonResponse
    {
        // Check ownership
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền cập nhật địa chỉ này',
            ], 403);
        }

        $request->validate([
            'label' => 'nullable|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:2',
            'is_default' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // If this is set as default, unset other defaults
            if ($request->boolean('is_default')) {
                $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật địa chỉ',
                'data' => new AddressResource($address),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật địa chỉ',
            ], 500);
        }
    }

    /**
     * Xóa địa chỉ
     */
    public function destroy(Request $request, Address $address): JsonResponse
    {
        // Check ownership
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền xóa địa chỉ này',
            ], 403);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa địa chỉ',
        ]);
    }

    /**
     * Đặt địa chỉ làm mặc định
     */
    public function setDefault(Request $request, Address $address): JsonResponse
    {
        // Check ownership
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền thay đổi địa chỉ này',
            ], 403);
        }

        DB::beginTransaction();

        try {
            $request->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã đặt làm địa chỉ mặc định',
                'data' => new AddressResource($address),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
            ], 500);
        }
    }
}
