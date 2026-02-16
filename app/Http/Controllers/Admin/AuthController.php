<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Bắt buộc phải có dòng này

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // 2. Thử đăng nhập (Dùng guard mặc định là 'web')
        if (Auth::attempt($credentials)) {
            
            $request->session()->regenerate();

            // 3. Kiểm tra: Nếu là ADMIN thì cho vào Dashboard
            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            // 4. Nếu là khách thường -> Đuổi ra
            Auth::logout();
            return back()->withErrors([
                'email' => 'Bạn không có quyền truy cập Admin!',
            ]);
        }

        // 5. Sai email hoặc mật khẩu
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ]);
    }

    // Xử lý đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}