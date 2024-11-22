<?php

namespace App\Services\Admins;

use App\Models\Client;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClientService
{
    protected $client;
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getPaginatedClient()
    {
        try {
            return $this->client->orderByDesc('created_at')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to get paginated client list: ' . $e->getMessage());
            throw new Exception('Failed to get paginated client list');
        }
    }

    public function getAllClient()
    {
        try {
            return $this->client->orderByDesc('created_at')->get();
        } catch (Exception $e) {
            Log::error('Failed to get client list: ' . $e->getMessage());
            throw new Exception('Failed to get client list');
        }
    }

    public function getClientById($id)
    {
        try {
            return $this->client->find($id);
        } catch (Exception $e) {
            Log::error('Failed to find this client: ' . $e->getMessage());
            throw new Exception('Failed to find this client');
        }
    }

    public function getClientByName($name)
    {
        try {
            return $this->client->where('name', 'LIKE', '%' . $name . '%')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to find this client by name: ' . $e->getMessage());
            throw new Exception('Failed to find this client by name');
        }
    }

    public function getClientByPhone($phone)
    {
        try {
            return $this->client->where('phone', 'LIKE', '%' . $phone . '%')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to find this client by phone: ' . $e->getMessage());
            throw new Exception('Failed to find this client by phone');
        }
    }
    public function addNewClientByLink(array $data)
    {
        DB::beginTransaction();
        try {
            Log::info('Creating new client');
            $client = $this->client->create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'company_name' => $data['company_name'],
                'tax_number' => $data['tax_number'],
                'address' => $data['address'],
                'field' => $data['field'],
                'source' => 1,
            ]);

            DB::commit();
            return $client;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to add new client by link: " . $e->getMessage());
            throw new Exception('Failed to add new client by link');
        }
    }
    public function addNewClient(array $data, $request)
    {
        DB::beginTransaction();

        try {
            // Tạo khách hàng trước, lấy ID
            $client = $this->client->create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'company_name' => $data['company_name'],
                'tax_number' => $data['tax_number'],
                'address' => $data['address'],
                'field' => $data['field'],
                'source' => 0,
                'note' => $data['note'],
            ]);

            // Lưu ảnh (nếu có) sau khi tạo khách hàng
            $frontIdImages = $this->saveImages($request, 'front_id_image', 'client_id_images');
            $backIdImages = $this->saveImages($request, 'back_id_image', 'client_id_images');

            // Lưu ảnh "other_images"
            $otherImages = [];
            if ($request->hasFile('other_images')) {
                foreach ($request->file('other_images') as $image) {
                    // Lưu từng ảnh và thêm vào mảng
                    $path = $image->store('client_other_images', 'public');
                    $otherImages[] = $path;
                }
            }

            // Cập nhật lại thông tin ảnh vào khách hàng
            $client->update([
                'front_id_image' => $frontIdImages ? implode(',', $frontIdImages) : null,
                'back_id_image' => $backIdImages ? implode(',', $backIdImages) : null,
                'other_images' => $otherImages ? json_encode($otherImages) : null,
            ]);

            DB::commit();
            return $client;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to add new client: " . $e->getMessage());
            throw new Exception('Failed to add new client');
        }
    }



    public function updateClient(array $data, $id)
    {
        DB::beginTransaction();
        try {
            $client = $this->getClientById($id);
            if (!$client) {
                throw new Exception('Cannot find this client');
            }

            $client->update([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'company_name' => $data['company_name'],
                'tax_number' => $data['tax_number'],
                'address' => $data['address'],
                'note' => $data['note'],
            ]);

            DB::commit();
            return $client;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update this client profile: ' . $e->getMessage());
            throw $e; // Re-throw the exception for further handling
        }
    }

    public function deleteClient($id)
    {
        DB::beginTransaction();
        try {
            $client = $this->getClientById($id);
            if (Storage::disk('public')->exists($client->front_id_image)) {
                Storage::disk('public')->delete($client->front_id_image);
            }
            if (Storage::disk('public')->exists($client->back_id_image)) {
                Storage::disk('public')->delete($client->back_id_image);
            }
            if ($client->other_images) {
                $oldImages = json_decode($client->other_images, true); // Giải mã mảng JSON
                foreach ($oldImages as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }
            }

            $client->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete this client profile: ' . $e->getMessage());
            throw new Exception('Failed to delete this client profile');
        }
    }
    public function saveImages($request, string $inputName, string $directory = 'images'): ?array
    {
        $paths = [];

        // Kiểm tra xem có file không
        if ($request->hasFile($inputName)) {
            // Lấy tất cả các file hình ảnh
            $images = $request->file($inputName);

            if (!is_array($images)) {
                $images = [$images]; // Đưa vào mảng nếu chỉ có 1 ảnh
            }

            foreach ($images as $image) {
                // Tạo tên file duy nhất
                $filename = time() . uniqid() . '.' . $image->getClientOriginalExtension();

                // Lưu file ảnh vào storage
                $path = $image->storeAs($directory, $filename, 'public'); // Lưu vào thư mục public storage

                // Lưu đường dẫn vào mảng
                $paths[] = $path;
            }

            // Trả về danh sách các đường dẫn
            return $paths;
        }

        // Trả về null nếu không có file nào được gửi
        return null;
    }
}
