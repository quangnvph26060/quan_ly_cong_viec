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
        $otherFiles = json_decode($client->other_files, true);
        return view('admins.pages.client.edit', compact('client', 'otherImages', 'otherFiles'));
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
            $otherFiles = [];

            // Kiểm tra và xử lý ảnh mới
            if ($request->hasFile('other_images')) {
                foreach ($request->file('other_images') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());

                    // Nếu là ảnh, xử lý logic lưu ảnh mới
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'jfif'])) {
                        // Xóa ảnh cũ chỉ khi có ảnh mới
                        if (empty($otherImages) && $client->other_images) {
                            $oldImages = json_decode($client->other_images, true);
                            foreach ($oldImages as $oldImage) {
                                if (Storage::disk('public')->exists($oldImage)) {
                                    Storage::disk('public')->delete($oldImage);
                                }
                            }
                        }
                        // Lưu ảnh mới
                        $path = $file->storeAs('client_other_images', $fileName, 'public');
                        $otherImages[] = $path;
                    }

                    // Nếu là file PDF, xử lý logic lưu file mới
                    if ($extension === 'pdf') {
                        // Xóa file cũ chỉ khi có file mới
                        if (empty($otherFiles) && $client->other_files) {
                            $oldFiles = json_decode($client->other_files, true);
                            foreach ($oldFiles as $oldFile) {
                                if (Storage::disk('public')->exists($oldFile)) {
                                    Storage::disk('public')->delete($oldFile);
                                }
                            }
                        }
                        // Lưu file PDF mới
                        $path = $file->storeAs('client_other_files', $fileName, 'public');
                        $otherFiles[] = $path;
                    }
                }
            }

            // Cập nhật các trường trong database nếu có thay đổi
            if (!empty($otherImages)) {
                $client->other_images = json_encode($otherImages);
            }
            if (!empty($otherFiles)) {
                $client->other_files = json_encode($otherFiles);
            }

            // Chỉ lưu thay đổi nếu có
            if (!empty($otherImages) || !empty($otherFiles)) {
                $client->save();
            }

            // Cập nhật thông tin ảnh nếu có
            if (!empty($imagePaths)) {
                $client->update($imagePaths);
            }


            return response()->json(['success' => 'Cập nhật tài liệu thành công']);
        } catch (Exception $e) {
            Log::error('Failed to update client image: ' . $e->getMessage());
            return response()->json(['error' => 'Cập nhật ảnh thất bại'], 500);
        }
    }

    public function deleteFile($id, Request $request)
    {
        try {
            $client = Client::find($id);
            if (!$client) {
                return response()->json(['message' => 'Khách hàng không tồn tại'], 404);
            }
            $fileName = $request->input('fileName');
            if ($client->other_files) {
                $otherFiles = json_decode($client->other_files, true);

                if (Storage::disk('public')->exists($fileName)) {
                    Storage::disk('public')->delete($fileName);
                }

                $otherFiles = array_filter($otherFiles, function ($file) use ($fileName) {
                    return $file !== $fileName;
                });

                $otherFiles = array_values($otherFiles);
                $client->update(['other_files' => json_encode($otherFiles)]);
            } else {
                return response()->json(['message' => 'File không tồn tại'], 400);
            }
            return response()->json(['message' => 'Xóa file thành công'], 200);
        } catch (Exception $e) {
            Log::error('Failed to delete this file: ' . $e->getMessage());
            return response()->json(['message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
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
