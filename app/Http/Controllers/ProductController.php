<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    // نمایش صفحه لیست محصولات
    public function index()
    {
        // گرفتن محصولات به همراه ویژگی‌هایشان
        $products = Product::with('attributes')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Products/Index', [
            'products' => $products
        ]);
    }

    // ذخیره محصول جدید به همراه ویژگی‌ها
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            // اعتبارسنجی آرایه ویژگی‌ها
            'attributes' => 'nullable|array',
            'attributes.*.key' => 'required|string',
            'attributes.*.value' => 'required|string',
            'attributes.*.unit' => 'nullable|string',
            'attributes.*.price_increase' => 'required|numeric|min:0',
        ]);

        // ساخت محصول پایه
        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'marketable' => 1,
        ]);

        // اگر ویژگی خاصی ثبت شده بود، آن‌ها را هم ذخیره می‌کنیم
        if (!empty($validated['attributes'])) {
            $product->attributes()->createMany($validated['attributes']);
        }

        return back()->with('success', 'محصول جدید با موفقیت اضافه شد.');
    }


    // ویرایش محصول و ویژگی‌های آن
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'attributes' => 'nullable|array',
            'attributes.*.key' => 'required|string',
            'attributes.*.value' => 'required|string',
            'attributes.*.unit' => 'nullable|string',
            'attributes.*.price_increase' => 'required|numeric|min:0',
        ]);

        // آپدیت اطلاعات پایه محصول
        $product->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
        ]);

        // پاک کردن ویژگی‌های قبلی و جایگزین کردن با ویژگی‌های جدید
        $product->attributes()->delete();

        if (!empty($validated['attributes'])) {
            $product->attributes()->createMany($validated['attributes']);
        }

        return back()->with('success', 'محصول با موفقیت ویرایش شد.');
    }

    // حذف کامل محصول
    public function destroy(Product $product)
    {
        // به دلیل وجود on delete cascade در مایگریشن‌ها، ویژگی‌های متصل به این محصول هم خودکار پاک می‌شوند
        $product->delete();

        return back()->with('success', 'محصول با موفقیت حذف شد.');
    }
}
