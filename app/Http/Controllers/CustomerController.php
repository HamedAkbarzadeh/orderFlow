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
        $customers = Customer::orderBy('created_at', 'desc')->get();

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

        Customer::create($validated);

        return back()->with('success', 'مشتری جدید با موفقیت به لیست اضافه شد.');
    }
}
