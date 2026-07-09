<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    // داشبورد اصلی ادمین (منوی ابزارها)
    public function index()
    {
        return Inertia::render('Admin/Dashboard');
    }

    // صفحه لیست کاربران
    public function users()
    {
        // دریافت همه کاربران به جز خود ادمین
        $users = User::where('id', '!=', auth()->id())->latest()->get();
        return Inertia::render('Admin/Users', ['users' => $users]);
    }

    // تغییر وضعیت فعال بودن اکانت کاربر
    public function toggleActivation(User $user)
    {
        if ($user->activated_at) {
            $user->activated_at = null; // مسدود کردن
            $message = 'حساب کاربر مسدود شد.';
        } else {
            $user->activated_at = now(); // فعال کردن
            $message = 'حساب کاربر فعال شد.';
        }

        $user->save(); // ذخیره مستقیم در دیتابیس بدون درگیر شدن با fillable

        return back()->with('success', $message);
    }
}
