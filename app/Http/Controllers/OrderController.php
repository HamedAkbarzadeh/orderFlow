<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Inertia\Inertia;
use Morilog\Jalali\CalendarUtils;

class OrderController extends Controller
{
    // نمایش داشبورد و لیست سفارشات باز
    public function index(\Illuminate\Http\Request $request)
    {
        $statusFilter = $request->query('status', 'pending');
        $userId = auth()->id();
        // --- بخش اول: محاسبه آماری داشبورد ---
        // برای بهینه‌سازی، همه سفارشات و آیتم‌هایشان را یک‌بار می‌گیریم تا آمار را از روی آن‌ها حساب کنیم
        $allOrders = \App\Models\Order::with('items')->where('user_id', $userId)->get();

        $totalOrdersCount = $allOrders->count();
        $pendingOrdersCount = $allOrders->where('status', 'pending')->count();
        $deliveredOrdersCount = $allOrders->where('status', 'delivered')->count(); // تعداد کل فروش‌ها

        // محاسبه درآمد کل (مجموع پرداختی‌های فاکتورهای تحویل داده شده پس از کسر تخفیف)
        $totalRevenue = $allOrders->where('status', 'delivered')->sum(function ($order) {
            $itemsTotal = $order->items->sum(function ($item) {
                return $item->unit_price * $item->quantity;
            });
            return max(0, $itemsTotal - $order->discount);
        });

        $totalCustomersCount = \App\Models\Customer::where('user_id', $userId)->count();

        // بسته‌بندی آمار برای ارسال به فرانت‌اند
        $statistics = [
            'total_sales_count' => $deliveredOrdersCount,
            'total_orders_count' => $totalOrdersCount,
            'pending_orders_count' => $pendingOrdersCount,
            'total_revenue' => $totalRevenue,
            'total_customers_count' => $totalCustomersCount, // اضافه شد
        ];

        // --- بخش دوم: کوئری لیست فیلتر شده سفارشات برای نمایش در کارت‌ها ---
        $query = \App\Models\Order::with(['customer', 'items'])->where('user_id', $userId);

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $orders = $query->orderBy('delivery_date', 'asc')->get()->map(function ($order) {
            $totalPrice = $order->items->sum(function ($item) {
                return $item->unit_price * $item->quantity;
            });
            $order->total_price = $totalPrice;
            $order->final_price = max(0, $totalPrice - $order->discount);
            return $order;
        });

        return \Inertia\Inertia::render('Dashboard', [
            'orders' => $orders,
            'currentFilter' => $statusFilter,
            'statistics' => $statistics // ارسال آمار به ویو
        ]);
    }

    // رفتن به صفحه ثبت سفارش جدید
    public function create()
    {
        // گرفتن لیست تمام مشتریان به ترتیب حروف الفبا
        $customers = Customer::where('user_id', auth()->id())->orderBy('store_name', 'asc')->get();

        // گرفتن محصولاتی که موجود هستند، به همراه ویژگی‌های (Attributes) آن‌ها
        $products = Product::with('attributes')
            ->where('user_id', auth()->id())
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
        $validated = $this->validateOrder($request);

        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'user_id' => auth()->id(),
            'delivery_date' => $validated['delivery_date'],
            'installment_date' => $validated['installment_date'] ?? null,
            'payment_type' => $validated['payment_type'],
            'discount' => $validated['discount'] ?? 0,
            'status' => 'pending',
        ]);

        $this->syncOrderItemsAndSummary($order, $validated);

        return redirect()->route('dashboard')->with('success', 'سفارش با موفقیت ثبت شد.');
    }

    // نمایش فرم ویرایش یک سفارش
    public function edit(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('items');

        $customers = Customer::orderBy('store_name', 'asc')->get();
        $products = Product::with('attributes')
            ->where('marketable', 1)
            ->get();

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    // بروزرسانی یک سفارش موجود
    public function update(Request $request, Order $order)
    {
        // فقط پرزنتری که سفارش رو ثبت کرده می‌تونه ویرایشش کنه
        abort_if($order->user_id !== auth()->id(), 403);

        $validated = $this->validateOrder($request);

        $order->update([
            'customer_id' => $validated['customer_id'],
            'delivery_date' => $validated['delivery_date'],
            'installment_date' => $validated['installment_date'] ?? null,
            'payment_type' => $validated['payment_type'],
            'discount' => $validated['discount'] ?? 0,
        ]);

        // ساده‌ترین و مطمئن‌ترین راه برای همگام‌سازی سبد خرید: حذف اقلام قبلی و ثبت مجدد از روی داده جدید
        $order->items()->delete();

        $this->syncOrderItemsAndSummary($order, $validated);

        return redirect()->route('dashboard')->with('success', 'سفارش با موفقیت ویرایش شد.');
    }

    // حذف یک سفارش
    public function destroy(Order $order)
    {
        // فقط پرزنتری که سفارش رو ثبت کرده می‌تونه حذفش کنه
        abort_if($order->user_id !== auth()->id(), 403);

        $order->items()->delete();
        $order->delete();

        return back()->with('success', 'سفارش با موفقیت حذف شد.');
    }

    // تغییر وضعیت سفارش
    public function updateStatus(Request $request, \App\Models\Order $order)
    {
        $validated = $request->validate([
            // اضافه شدن وضعیت cancelled به لیست مجاز
            'status' => 'required|in:pending,delivered,cancelled'
        ]);

        // فقط پرزنتر مربوطه می‌تواند وضعیت سفارش خودش را تغییر دهد
        if ($order->user_id === auth()->id()) {
            $order->update(['status' => $validated['status']]);
        }

        return back()->with('success', 'وضعیت فاکتور به‌روزرسانی شد.');
    }

    /**
     * ولیدیشن مشترک بین ثبت سفارش جدید و ویرایش سفارش، تا قوانین یک‌بار نوشته بشن.
     */
    private function validateOrder(Request $request): array
    {
        return $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date',
            'payment_type' => 'required|string',
            'installment_date' => 'nullable|date|required_if:payment_type,قسطی',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_attribute_id' => 'nullable|exists:product_attributes,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);
    }

    /**
     * اقلام سفارش رو از روی دیتای ولیدیت‌شده می‌سازه و متن خلاصه سفارش (summary_text) رو
     * تولید و ذخیره می‌کنه. هم برای ثبت سفارش جدید استفاده میشه هم برای ویرایش.
     */
    private function syncOrderItemsAndSummary(Order $order, array $validated): void
    {
        $customer = Customer::find($validated['customer_id']);

        $summaryText = "سفارش برای: {$customer->store_name} (آقای/خانم {$customer->contact_name})\n";
        $summaryText .= "آدرس: {$customer->address}\n";
        $summaryText .= "تاریخ تحویل: {$this->toJalaliDate($validated['delivery_date'], true)}\n";
        $summaryText .= "نوع پرداخت: {$validated['payment_type']}\n";
        if (!empty($validated['installment_date'])) {
            $summaryText .= "تاریخ سررسید قسط: {$this->toJalaliDate($validated['installment_date'], true)}\n";
        }
        $summaryText .= "\nاقلام:\n";

        $totalPrice = 0;

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $unitPrice = $product->price;

            $attributeName = '';
            if (!empty($item['product_attribute_id'])) {
                $attribute = \App\Models\ProductAttribute::find($item['product_attribute_id']);
                $unitPrice += $attribute->price_increase;
                $attributeName = " ({$attribute->key}: {$attribute->value} {$attribute->unit})";
            }

            $order->items()->create([
                'product_id' => $product->id,
                'product_attribute_id' => $item['product_attribute_id'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
            ]);

            $totalPrice += ($unitPrice * $item['quantity']);

            $formattedUnitPrice = number_format($unitPrice);
            $summaryText .= "- {$item['quantity']} عدد/واحد {$product->name} {$attributeName} (فی: {$formattedUnitPrice} ریال)\n";
        }

        $discount = $validated['discount'] ?? 0;
        $finalPrice = max(0, $totalPrice - $discount);

        $summaryText .= "\n-------------------\n";
        $summaryText .= "مبلغ کل: " . number_format($totalPrice) . " ریال\n";
        if ($discount > 0) {
            $summaryText .= "تخفیف: " . number_format($discount) . " ریال\n";
        }
        $summaryText .= "مبلغ قابل پرداخت: " . number_format($finalPrice) . " ریال\n";

        $order->update(['summary_text' => $summaryText]);
    }

    /**
     * تبدیل تاریخ میلادی (فرمت Y-m-d) به رشته تاریخ شمسی
     * برای استفاده در متن خلاصه سفارش که برای مشتری ارسال/کپی می‌شود.
     *
     * @param string $gregorianDate تاریخ میلادی، مثلاً 2026-07-08
     * @param bool $long اگر true باشد خروجی با نام ماه است (مثلاً «۱۷ تیر ۱۴۰۵»)، در غیر این صورت عددی (۱۴۰۵/۰۴/۱۷)
     */
    private function toJalaliDate(string $gregorianDate, bool $long = false): string
    {
        $jalaliMonths = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];

        [$year, $month, $day] = explode('-', $gregorianDate);

        [$jYear, $jMonth, $jDay] = CalendarUtils::toJalali(
            (int) $year,
            (int) $month,
            (int) $day
        );

        if ($long) {
            return "{$jDay} {$jalaliMonths[$jMonth]} {$jYear}";
        }

        return sprintf('%04d/%02d/%02d', $jYear, $jMonth, $jDay);
    }
}
