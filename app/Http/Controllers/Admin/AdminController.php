<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Admins\UserService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function list(Request $request)
    {
        $inputs = $request->all();
        $users = Admin::query();
        if (isset($request->name) && $request->name != '') {
            $users->where('full_name', 'like', '%' . $request->name . '%')
                  ->orWhere('email', 'like', '%' . $request->name . '%');
                
        }

        return view('admins.pages.admins.list', [
            "users" => $users->latest()->paginate(20),
            "inputs" => $inputs
        ]);
    }
    public function add()
    {
        return view('admins.pages.admins.add');
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'password' => 'required|string|min:6',
                'level' => 'required|in:1,3',
            ]);
            $inputs = [
                'full_name'  => $validated['full_name'],
                'username'   => $validated['full_name'],
                'password'   => bcrypt($validated['password']),
                'level'      => (int) $validated['level'],
                'role_code'  => $validated['level'] == 3 ? 'QLHD' : 'admin_mkt',
            ];
    
            Admin::create($inputs);
    
            return redirect()->back()->with('success', 'Thêm người dùng thành công!');
        } catch (\Throwable $th) {
            \Log::error('Lỗi khi thêm người dùng: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'input' => $request->all(),
            ]);
    
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi thêm người dùng. Vui lòng thử lại.');
        }
    }
    public function delete($userId)
    {
      
        $user = Admin::find($userId);
        if ($user) {

            $user->status = $user->status === 1 ? 0 : 1;
            
            $user->save();

            return back()->with('success', 'Trạng thái đã được thay đổi thành công');
        }
        return back()->with('error', 'Người dùng không tồn tại');
    }

}
