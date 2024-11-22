<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Client;
use App\Services\Admins\ClientService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClientImageController extends Controller
{
    protected $clientService;
    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function editImage($id)
    {
        $client = Client::find($id);
        $otherImages = json_decode($client->other_images, true);
        return view('admins.pages.client.edit', compact('client', 'otherImages'));
    }

    public function updateImage(Request $request, $id)
    {
        try {
            // Tìm khách hàng theo id
            $client = Client::find($id);

            // Kiểm tra nếu không tìm thấy khách hàng
            if (!$client) {
                return response()->json([
                    'error' => 'Khách hàng không tồn tại'
                ], 404);
            }

            // Khởi tạo mảng chứa đường dẫn ảnh
            $imagePaths = [];

            // Kiểm tra nếu có file front_id_image và lưu ảnh
            if ($request->hasFile('front_id_image')) {
                // Xóa ảnh cũ nếu có
                if (Storage::disk('public')->exists($client->front_id_image)) {
                    Storage::disk('public')->delete($client->front_id_image);
                }
                // Lưu ảnh mới
                $frontImagePaths = $this->clientService->saveImages($request, 'front_id_image', 'client_id_images');
                $imagePaths['front_id_image'] = $frontImagePaths[0] ?? null;
            }

            // Kiểm tra nếu có file back_id_image và lưu ảnh
            if ($request->hasFile('back_id_image')) {
                // Xóa ảnh cũ nếu có
                if (Storage::disk('public')->exists($client->back_id_image)) {
                    Storage::disk('public')->delete($client->back_id_image);
                }
                // Lưu ảnh mới
                $backImagePaths = $this->clientService->saveImages($request, 'back_id_image', 'client_id_images');
                $imagePaths['back_id_image'] = $backImagePaths[0] ?? null;
            }

            // Xử lý các ảnh khác
            $otherImages = [];
            if ($request->hasFile('other_images')) {
                // Xóa các ảnh cũ trong storage
                if ($client->other_images) {
                    $oldImages = json_decode($client->other_images, true); // Giải mã mảng JSON
                    foreach ($oldImages as $oldImage) {
                        if (Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }

                // Lưu từng ảnh mới và thêm vào mảng
                foreach ($request->file('other_images') as $image) {
                    $path = $image->store('client_other_images', 'public');
                    $otherImages[] = $path;
                }
            }

            // Cập nhật thông tin ảnh nếu có
            if (!empty($imagePaths)) {
                $client->update($imagePaths);
            }

            // Cập nhật các ảnh khác dưới dạng JSON
            if (!empty($otherImages)) {
                // Lưu các đường dẫn ảnh khác vào trường `other_images` dưới dạng JSON
                $client->other_images = json_encode($otherImages);
                $client->save();
            }

            return response()->json(['success' => 'Cập nhật ảnh thành công']);
        } catch (Exception $e) {
            Log::error('Failed to update client image: ' . $e->getMessage());
            return response()->json(['error' => 'Cập nhật ảnh thất bại'], 500);
        }
    }


    public function deleteImage($id, Request $request)
    {
        try {
            $client = Client::find($id);
            if (!$client) {
                return response()->json(['message' => 'Khách hàng không tồn tại'], 404);
            }

            $type = $request->input('type');
            $imageName = $request->input('imageName');
            if ($type === 'front' && $client->front_id_image) {
                if (Storage::disk('public')->exists($client->front_id_image)) {
                    Storage::disk('public')->delete($client->front_id_image);
                }
                $client->update(['front_id_image' => null]);
            } elseif ($type === 'back' && $client->back_id_image) {
                if (Storage::disk('public')->exists($client->back_id_image)) {
                    Storage::disk('public')->delete($client->back_id_image);
                }
                $client->update(['back_id_image' => null]);
            } elseif ($type === 'other' && $client->other_images) {
                $otherImages = json_decode($client->other_images, true);

                // Kiểm tra nếu ảnh tồn tại và xóa nó
                if (Storage::disk('public')->exists($imageName)) {
                    Storage::disk('public')->delete($imageName);
                }

                // Loại bỏ ảnh khỏi mảng
                $otherImages = array_filter($otherImages, function ($image) use ($imageName) {
                    return $image !== $imageName;
                });

                // Đảm bảo các chỉ mục mảng liên tiếp và cập nhật lại vào cơ sở dữ liệu
                $otherImages = array_values($otherImages);
                $client->update(['other_images' => json_encode($otherImages)]);
            } else {
                return response()->json(['message' => 'Ảnh không tồn tại hoặc loại không hợp lệ'], 400);
            }

            return response()->json(['message' => 'Xóa ảnh thành công'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
        }
    }
}
