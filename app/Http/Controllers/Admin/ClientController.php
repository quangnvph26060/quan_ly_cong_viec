<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Client;
use App\Services\Admins\ClientService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    protected $clientService;
    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request)
    {
        try {
          
            $clients = $this->clientService->getPaginatedClient();
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admins.pages.client.table', compact('clients'))->render(),
                    'pagination' => $clients->links('pagination::bootstrap-4')->toHtml()
                ]);
            }

            return view('admins.pages.client.index', compact('clients'));
        } catch (Exception $e) {
            Log::error('Failed to get paginated Client list: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get paginated Client list'], 500);
        }
    }


    public function add()
    {
        return view('admins.pages.client.add');
    }

    public function store(Request $request)
    {
        // Validation cho các trường dữ liệu và ảnh
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15|unique:clients,phone',
            'email' => 'nullable|email',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'field' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'front_id_image' => 'nullable|mimes:jpeg,jpg,png,gif|max:2048',
            'back_id_image' => 'nullable|mimes:jpeg,jpg,png,gif|max:2048',
            'other_images.*' => 'nullable|mimes:jpeg,jpg,png,gif,pdf',
        ], [
            'phone.unique' => 'Số điện thoại đã tồn tại trong cơ sở dữ liệu',
            'front_id_image.mimes' => 'Chỉ chấp nhận file hình ảnh với định dạng: jpeg, jpg, png, gif.',
            'back_id_image.mimes' => 'Chỉ chấp nhận file hình ảnh với định dạng: jpeg, jpg, png, gif.',
            'front_id_image.max' => 'Kích thước ảnh mặt trước không được vượt quá 10MB.',
            'back_id_image.max' => 'Kích thước ảnh mặt sau không được vượt quá 10MB.',
            'other_images.*.mimes' => 'Các file ảnh khác chỉ được có định dạng: jpeg, jpg, png, gif.',
            'other_images.*.max' => 'Kích thước mỗi file ảnh khác không được vượt quá 2MB.',
        ]);

        try {
            // Lưu khách hàng và xử lý ảnh
            $client = $this->clientService->addNewClient($validatedData, $request);

            // Lấy danh sách khách hàng đã được cập nhật và HTML phân trang
            $clients = $this->clientService->getPaginatedClient(); // Lấy danh sách khách hàng với phân trang

            // Trả về phản hồi JSON với HTML cập nhật cho bảng và phân trang
            return response()->json([
                'success' => 'Thêm khách hàng mới thành công',
                'html' => view('admins.pages.client.table', ['clients' => $clients])->render(),
                'pagination' => $clients->links('pagination::bootstrap-4')->render(),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to add new Client: ' . $e->getMessage());
            return response()->json([
                'error' => 'Thêm khách hàng không thành công'
            ], 500);
        }
    }


    public function addByLink()
    {
        return view('admins.pages.client.link');
    }

    public function storeByLink(Request $request)
    {
        try {
            $client = $this->clientService->addNewClientByLink($request->all());
            return redirect()->back()->with('success', 'Đăng ký thành công');
        } catch (Exception $e) {
            Log::error("Failed to create Client By Link: " . $e->getMessage());
            return ApiResponse::error('Failed to create Client By Link', 500);
        }
    }

    public function edit($id)
    {
        try {
            $client = $this->clientService->getClientById($id);
            return response()->json($client);
        } catch (Exception $e) {
            Log::error('Failed to load client data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load client data'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'company_name' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'field' => 'nullable',
                'note' => 'nullable',
            ]);
            // Pass the validated data as an array
            $client = $this->clientService->updateClient($validatedData, $id);

            $clients = $this->clientService->getPaginatedClient();

            return response()->json([
                'success' => 'Thông tin khách hàng đã được cập nhật thành công',
                'html' => view('admins.pages.client.table', ['clients' => $clients])->render(),
                'pagination' => $clients->links('pagination::bootstrap-4')->render(),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update Client: ' . $e->getMessage());
            return response()->json([
                'error' => 'Cập nhật khách hàng không thành công'
            ], 500);
        }
    }


    public function delete($id)
    {
        try {
            $this->clientService->deleteClient($id);
            return response()->json(['success' => 'Xóa khách hàng thành công']);
        } catch (Exception $e) {
            Log::error('Failed to delete this client: ' . $e->getMessage());
            return response()->json(['error' => 'Xóa khách hàng thất bại'], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            // Xử lý tìm kiếm theo tên hoặc số điện thoại
            if (preg_match('/\d/', $query)) {
                $clients = $this->clientService->getClientByPhone($query);
            } else {
                $clients = $this->clientService->getClientByName($query);
            }

            if ($request->ajax()) {
                $html = view('admins.pages.client.table', compact('clients'))->render();
                $pagination = $clients->appends(['query' => $query])->links('pagination::bootstrap-4')->render();
                return response()->json(['html' => $html, 'pagination' => $pagination]);
            }

            return view('admins.pages.client.index', compact('clients'));
        } catch (Exception $e) {
            Log::error('Failed to search clients: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to search clients'], 500);
        }
    }
}
