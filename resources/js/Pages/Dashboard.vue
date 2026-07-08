<script setup lang="ts">
import MainLayout from "@/Layouts/MainLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

// تعریف تایپ‌های دیتای دریافتی از بک‌اند
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
    summary_text: string | null;
    customer: Customer;
}

// دریافت لیست سفارشات از OrderController
const props = defineProps<{
    orders: Order[];
}>();

// فرم برای تغییر وضعیت سفارش به "تحویل داده شد"
const form = useForm({
    status: "delivered",
});

const markAsDelivered = (orderId: number) => {
    if (confirm("آیا مطمئن هستید که این سفارش تحویل داده شده است؟")) {
        form.patch(route("orders.status", orderId), {
            preserveScroll: true,
        });
    }
};

// تابع کمکی برای نمایش زیباتر تاریخ
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("fa-IR");
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

                <div class="flex justify-between items-start mb-3 pl-1 pr-3">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">
                            {{ order.customer.store_name }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            مسئول: {{ order.customer.contact_name }}
                        </p>
                    </div>
                    <div
                        class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-lg text-xs font-bold flex items-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-3.5 h-3.5 ml-1"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        {{ formatDate(order.delivery_date) }}
                    </div>
                </div>

                <div
                    class="bg-slate-50/80 p-3 rounded-2xl text-xs text-slate-600 leading-relaxed mb-4 border border-slate-100 whitespace-pre-wrap"
                >
                    {{ order.summary_text }}
                </div>

                <div class="flex items-center justify-between mt-2 pl-1 pr-3">
                    <span
                        class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-md"
                    >
                        پرداخت: {{ order.payment_type }}
                    </span>

                    <button
                        @click="markAsDelivered(order.id)"
                        :disabled="form.processing"
                        class="flex items-center text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl hover:bg-emerald-100 transition-colors"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-4 h-4 ml-1"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12.75l6 6 9-13.5"
                            />
                        </svg>
                        تحویل دادم
                    </button>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
