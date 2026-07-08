<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Inertia\Inertia;

class OrderController extends Controller
{
    // نمایش داشبورد و لیست سفارشات باز
    public function index()
    {
        // سفارشات را بر اساس نزدیک‌ترین تاریخ تحویل مرتب می‌کنیم
        $orders = Order::with('customer')
            ->where('status', 'pending')
            ->orderBy('delivery_date', 'asc')
            ->get();

        return Inertia::render('Dashboard', [
            'orders' => $orders
        ]);
    }

    // رفتن به صفحه ثبت سفارش جدید
    public function create()
    {
        // گرفتن لیست تمام مشتریان به ترتیب حروف الفبا
        $customers = Customer::orderBy('store_name', 'asc')->get();

        // گرفتن محصولاتی که موجود هستند، به همراه ویژگی‌های (Attributes) آن‌ها
        $products = Product::with('attributes')
            ->where('marketable', 1)
            ->get();

        // پاس دادن دیتا به کامپوننت Vue از طریق Inertia
        return Inertia::render('Orders/Create', [
            'customers' => $customers,
            'products' => $products
        ]);
    }


    // ذخیره سفارش جدید در دیتابیس
    public function store(Request $request)
    {
        // ۱. اعتبارسنجی اطلاعات ارسالی از سمت کاربر
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date',
            'payment_type' => 'required|string',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1', // حداقل یک محصول باید انتخاب شود
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_attribute_id' => 'nullable|exists:product_attributes,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        // ۲. ایجاد فاکتور اصلی
        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'user_id' => auth()->id(), // شناسه پرزنتری که لاگین کرده است
            'delivery_date' => $validated['delivery_date'],
            'payment_type' => $validated['payment_type'],
            'discount' => $validated['discount'] ?? 0,
            'status' => 'pending',
        ]);

        // ۳. پردازش اقلام سفارش و ساخت متن خلاصه
        $customer = Customer::find($validated['customer_id']);
        $summaryText = "سفارش برای: {$customer->store_name} (آقای/خانم {$customer->contact_name})\n";
        $summaryText .= "آدرس: {$customer->address}\n";
        $summaryText .= "تاریخ تحویل: {$validated['delivery_date']}\n\nاقلام:\n";

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $unitPrice = $product->price; // قیمت پایه محصول

            $attributeName = '';
            // اگر محصول ویژگی خاصی (مثل رم یا وزن) داشت، قیمت آن به قیمت پایه اضافه می‌شود
            if (!empty($item['product_attribute_id'])) {
                $attribute = \App\Models\ProductAttribute::find($item['product_attribute_id']);
                $unitPrice += $attribute->price_increase;
                $attributeName = " ({$attribute->key}: {$attribute->value} {$attribute->unit})";
            }

            // ثبت آیتم در دیتابیس
            $order->items()->create([
                'product_id' => $product->id,
                'product_attribute_id' => $item['product_attribute_id'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
            ]);

            // اضافه کردن به متن خلاصه
            $summaryText .= "- {$item['quantity']} عدد/واحد {$product->name} {$attributeName}\n";
        }

        // ۴. بروزرسانی فاکتور با متن خلاصه تولید شده
        $order->update(['summary_text' => $summaryText]);

        // ۵. ریدایرکت کاربر به داشبورد
        return redirect()->route('dashboard')->with('success', 'سفارش با موفقیت ثبت شد.');
    }

    // تغییر وضعیت سفارش (مثلاً تیک زدن تحویل داده شد)
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,delivered'
        ]);

        // فقط پرزنتر مربوطه می‌تواند وضعیت سفارش خودش را تغییر دهد
        if ($order->user_id === auth()->id()) {
            $order->update(['status' => $validated['status']]);
        }

        return back()->with('success', 'وضعیت فاکتور به‌روزرسانی شد.');
    }
}
