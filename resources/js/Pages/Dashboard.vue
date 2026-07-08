<script setup lang="ts">
import { ref } from "vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { toJalaliDisplay } from "@/utils/jalali";

// تایپ‌های دیتای دریافتی
interface Customer {
    id: number;
    store_name: string;
    contact_name: string;
}

interface Order {
    id: number;
    delivery_date: string;
    payment_type: string;
    status: string;
    summary_text: string;
    discount: number; // اضافه شد
    total_price: number; // اضافه شد
    final_price: number; // اضافه شد
    customer: Customer;
}

interface Statistics {
    total_sales_count: number;
    total_orders_count: number;
    pending_orders_count: number;
    total_revenue: number;
    total_customers_count: number; // اضافه شد
}

const props = defineProps<{
    orders: Order[];
    currentFilter: string;
    statistics: Statistics; // دریافت دیتا از کنترلر
}>();

// تابع به‌روزرسانی وضعیت سفارش
const updateOrderStatus = (orderId: number, newStatus: string) => {
    let message = "";

    if (newStatus === "delivered") {
        message = "آیا مطمئن هستید که این سفارش تحویل داده شده است؟";
    } else if (newStatus === "cancelled") {
        message = "آیا از لغو کردن این سفارش اطمینان دارید؟";
    } else {
        message =
            'آیا می‌خواهید این سفارش را مجدداً به حالت "باز" (در انتظار) برگردانید؟';
    }

    if (confirm(message)) {
        router.patch(
            route("orders.status", orderId),
            { status: newStatus },
            {
                preserveScroll: true,
            },
        );
    }
};

// تابع کپی متن در کلیپ‌بورد
const copiedOrderId = ref<number | null>(null);

const copyToClipboard = async (text: string, orderId: number) => {
    try {
        await navigator.clipboard.writeText(text);
        // نمایش پیام "کپی شد" برای 2 ثانیه
        copiedOrderId.value = orderId;
        setTimeout(() => {
            copiedOrderId.value = null;
        }, 2000);
    } catch (err) {
        alert(
            "خطا در کپی کردن متن. لطفاً دسترسی کلیپ‌بورد مرورگر را بررسی کنید.",
        );
    }
};

const changeFilter = (status: string) => {
    router.get(
        route("dashboard"),
        { status },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const formatDate = (dateString: string) => {
    return toJalaliDisplay(dateString);
};
</script>

<template>
    <Head title="داشبورد - لیست سفارشات" />

    <MainLayout>
        <div class="mb-6 mt-2">
            <h2 class="text-2xl font-black text-slate-800">
                سلام {{ $page.props.auth.user.name }}! 👋
            </h2>
            <p class="text-slate-500 text-sm mt-1">
                لیست سفارشات باز و زمان‌بندی تحویل شما
            </p>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-6 relative z-10">
            <div
                class="bg-blue-500/10 backdrop-blur-md border border-blue-500/20 p-3.5 rounded-2xl shadow-sm flex flex-col justify-center"
            >
                <div class="flex items-center gap-2 mb-1">
                    <div class="bg-blue-500/20 p-1.5 rounded-lg text-blue-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
                            />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-slate-600"
                        >مشتریان</span
                    >
                </div>
                <span class="text-xl font-black text-blue-600 mr-1">{{
                    statistics.total_customers_count
                }}</span>
            </div>
            <div
                class="bg-emerald-500/10 backdrop-blur-md border border-emerald-500/20 p-3.5 rounded-2xl shadow-sm flex flex-col justify-center"
            >
                <div class="flex items-center gap-2 mb-1">
                    <div
                        class="bg-emerald-500/20 p-1.5 rounded-lg text-emerald-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12.75l6 6 9-13.5"
                            />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-slate-600"
                        >فروش موفق</span
                    >
                </div>
                <span class="text-xl font-black text-emerald-600 mr-1">{{
                    statistics.total_sales_count
                }}</span>
            </div>

            <div
                class="bg-amber-500/10 backdrop-blur-md border border-amber-500/20 p-3.5 rounded-2xl shadow-sm flex flex-col justify-center"
            >
                <div class="flex items-center gap-2 mb-1">
                    <div
                        class="bg-amber-500/20 p-1.5 rounded-lg text-amber-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-slate-600"
                        >سفارشات باز</span
                    >
                </div>
                <span class="text-xl font-black text-amber-600 mr-1">{{
                    statistics.pending_orders_count
                }}</span>
            </div>

            <div
                class="bg-indigo-500/10 backdrop-blur-md border border-indigo-500/20 p-3.5 rounded-2xl shadow-sm flex flex-col justify-center"
            >
                <div class="flex items-center gap-2 mb-1">
                    <div
                        class="bg-indigo-500/20 p-1.5 rounded-lg text-indigo-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12h3.75M9 15h3.75M9 15.75h3.75M18 9.75v-.75a2.25 2.25 0 00-2.25-2.25h-7.5a2.25 2.25 0 00-2.25 2.25v.75m12 0v1.5c0 .621-.504 1.125-1.125 1.125H18m0-2.625h1.5a.75.75 0 01.75.75v10.5a.75.75 0 01-.75.75h-1.5m-12-12h-1.5a.75.75 0 00-.75.75v10.5c0 .414.336.75.75.75h1.5"
                            />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-slate-600"
                        >کل سفارشات</span
                    >
                </div>
                <span class="text-xl font-black text-indigo-600 mr-1">{{
                    statistics.total_orders_count
                }}</span>
            </div>

            <div
                class="col-span-2 sm:col-span-1 bg-gradient-to-br from-indigo-600 to-violet-700 p-4 rounded-2xl shadow-lg shadow-indigo-200 text-white flex justify-between items-center relative overflow-hidden"
            >
                <div
                    class="absolute -right-4 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"
                ></div>

                <div>
                    <span class="text-xs font-medium text-indigo-100 mb-1 block"
                        >مجموع درآمد (فروش‌های موفق)</span
                    >
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black tracking-tight">{{
                            statistics.total_revenue.toLocaleString()
                        }}</span>
                        <span class="text-xs text-indigo-200">ریال</span>
                    </div>
                </div>
                <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-6 h-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="flex bg-white/40 backdrop-blur-md p-1.5 rounded-2xl mb-6 border border-white/60 shadow-sm relative z-10 overflow-x-auto"
        >
            <button
                @click="changeFilter('all')"
                class="min-w-max flex-1 py-2 px-2 text-[11px] sm:text-xs font-bold rounded-xl transition-all duration-300"
                :class="
                    currentFilter === 'all'
                        ? 'bg-white text-indigo-600 shadow-sm'
                        : 'text-slate-500 hover:text-slate-700'
                "
            >
                همه
            </button>
            <button
                @click="changeFilter('pending')"
                class="min-w-max flex-1 py-2 px-2 text-[11px] sm:text-xs font-bold rounded-xl transition-all duration-300"
                :class="
                    currentFilter === 'pending'
                        ? 'bg-white text-amber-600 shadow-sm'
                        : 'text-slate-500 hover:text-slate-700'
                "
            >
                سفارشات باز
            </button>
            <button
                @click="changeFilter('delivered')"
                class="min-w-max flex-1 py-2 px-2 text-[11px] sm:text-xs font-bold rounded-xl transition-all duration-300"
                :class="
                    currentFilter === 'delivered'
                        ? 'bg-white text-emerald-600 shadow-sm'
                        : 'text-slate-500 hover:text-slate-700'
                "
            >
                تحویل شده
            </button>
            <button
                @click="changeFilter('cancelled')"
                class="min-w-max flex-1 py-2 px-2 text-[11px] sm:text-xs font-bold rounded-xl transition-all duration-300"
                :class="
                    currentFilter === 'cancelled'
                        ? 'bg-white text-rose-600 shadow-sm'
                        : 'text-slate-500 hover:text-slate-700'
                "
            >
                لغو شده
            </button>
        </div>

        <div
            v-if="orders.length === 0"
            class="flex flex-col items-center justify-center p-8 mt-10 bg-white/50 backdrop-blur-sm rounded-3xl border border-white/60 shadow-sm text-center"
        >
            <div
                class="w-16 h-16 bg-indigo-50 text-indigo-300 rounded-full flex items-center justify-center mb-4"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-8 h-8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 15.75h3.75M18 9.75v-.75a2.25 2.25 0 00-2.25-2.25h-7.5a2.25 2.25 0 00-2.25 2.25v.75m12 0v1.5c0 .621-.504 1.125-1.125 1.125H18m0-2.625h1.5a.75.75 0 01.75.75v10.5a.75.75 0 01-.75.75h-1.5m-12-12h-1.5a.75.75 0 00-.75.75v10.5c0 .414.336.75.75.75h1.5m-12-12v1.5c0 .621.504 1.125 1.125 1.125H3m12-2.625h1.5a.75.75 0 01.75.75v10.5a.75.75 0 01-.75.75h-1.5m-12-12h-1.5a.75.75 0 00-.75.75v10.5c0 .414.336.75.75.75h1.5"
                    />
                </svg>
            </div>
            <h3 class="text-slate-700 font-bold mb-1">
                هیچ سفارشی در انتظار نیست!
            </h3>
            <p class="text-slate-500 text-xs mb-4">
                برای ثبت سفارش جدید روی دکمه زیر کلیک کنید.
            </p>
            <Link
                :href="route('orders.create')"
                class="bg-indigo-600 text-white px-5 py-2 rounded-xl text-sm font-semibold shadow-lg shadow-indigo-200"
            >
                ثبت سفارش جدید
            </Link>
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="order in orders"
                :key="order.id"
                class="bg-white/70 backdrop-blur-md border border-white/50 p-4 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden"
            >
                <div
                    class="absolute right-0 top-0 bottom-0 w-1.5 bg-indigo-500 rounded-r-3xl"
                ></div>

                <div
                    class="relative bg-slate-50/80 p-3 pt-8 sm:pt-3 sm:pl-10 rounded-2xl text-xs text-slate-600 leading-relaxed mb-4 border border-slate-100"
                >
                    <div class="whitespace-pre-wrap">
                        {{ order.summary_text }}
                    </div>

                    <div
                        class="bg-white p-3 rounded-xl border border-slate-100 mb-4 shadow-sm mx-1 mt-2"
                    >
                        <div
                            class="flex justify-between items-center mb-1.5 text-xs"
                        >
                            <span class="text-slate-500 font-medium"
                                >مبلغ کل سفارش:</span
                            >
                            <span
                                class="font-bold"
                                :class="
                                    order.discount > 0
                                        ? 'line-through text-slate-400'
                                        : 'text-slate-700'
                                "
                            >
                                {{ order.total_price.toLocaleString() }} ریال
                            </span>
                        </div>

                        <div
                            v-if="order.discount > 0"
                            class="flex justify-between items-center mb-1.5 text-xs text-rose-500"
                        >
                            <span class="font-medium">تخفیف اعمال شده:</span>
                            <span class="font-bold"
                                >{{
                                    order.discount.toLocaleString()
                                }}
                                ریال</span
                            >
                        </div>

                        <div
                            class="flex justify-between items-center mt-2 pt-2 border-t border-slate-100"
                        >
                            <span class="text-xs font-black text-indigo-800"
                                >مبلغ قابل پرداخت:</span
                            >
                            <span class="text-sm font-black text-indigo-600"
                                >{{
                                    order.final_price.toLocaleString()
                                }}
                                ریال</span
                            >
                        </div>
                    </div>

                    <button
                        @click="copyToClipboard(order.summary_text, order.id)"
                        class="absolute top-2 left-2 flex items-center justify-center gap-1 bg-white border border-slate-200 text-slate-500 px-2 py-1.5 rounded-lg shadow-sm hover:text-indigo-600 transition-colors active:scale-95"
                    >
                        <span
                            v-if="copiedOrderId === order.id"
                            class="text-emerald-500 text-[10px] font-bold"
                            >کپی شد! ✓</span
                        >
                        <template v-else>
                            <span
                                class="text-[10px] font-semibold hidden sm:inline"
                                >کپی</span
                            >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="w-4 h-4"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"
                                />
                            </svg>
                        </template>
                    </button>
                </div>

                <div class="flex items-center justify-between mt-2 pl-1 pr-3">
                    <span
                        class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-md"
                    >
                        پرداخت: {{ order.payment_type }}
                    </span>

                    <div class="flex gap-2">
                        <template v-if="order.status === 'pending'">
                            <button
                                @click="
                                    updateOrderStatus(order.id, 'cancelled')
                                "
                                class="flex items-center text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-1.5 rounded-xl hover:bg-rose-100 transition-colors"
                            >
                                ✕ لغو
                            </button>
                            <button
                                @click="
                                    updateOrderStatus(order.id, 'delivered')
                                "
                                class="flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl hover:bg-emerald-100 transition-colors"
                            >
                                ✓ تحویل دادم
                            </button>
                        </template>

                        <template v-else>
                            <button
                                @click="updateOrderStatus(order.id, 'pending')"
                                class="flex items-center text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-xl hover:bg-amber-100 transition-colors"
                            >
                                ↻ برگشت به باز
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
