<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Inertia\Inertia;

class CustomerController extends Controller
{
    // نمایش صفحه لیست مشتریان
    public function index()
    {
        // دریافت مشتریان به ترتیب جدیدترین‌ها
        $customers = Customer::where('user_id', auth()->id())->latest()->get();

        return Inertia::render('Customers/Index', [
            'customers' => $customers
        ]);
    }

    // متد store که قبلا نوشته بودیم اینجا قرار دارد...
    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);
        $validated['user_id'] = auth()->id();
        Customer::create($validated);

        return back()->with('success', 'مشتری جدید با موفقیت به لیست اضافه شد.');
    }


    // ویرایش اطلاعات مشتری
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $customer->update($validated);

        return back()->with('success', 'اطلاعات مشتری با موفقیت ویرایش شد.');
    }

    // حذف مشتری
    public function destroy(Customer $customer)
    {
        // نکته: اگر این مشتری فاکتوری داشته باشد، به دلیل وابستگی اطلاعات،
        // در پروژه‌های واقعی معمولا به جای حذف کامل از Soft Delete استفاده می‌شود.
        // اما اینجا طبق ساختار فعلی حذف کامل را پیاده‌سازی می‌کنیم.
        $customer->delete();

        return back()->with('success', 'مشتری با موفقیت حذف شد.');
    }
}
