<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";

// این پراپ‌ها به صورت پیش‌فرض توسط روت '/' لاراول به این صفحه پاس داده می‌شوند
defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="خوش آمدید" />

    <div
        class="relative min-h-screen flex items-center justify-center bg-slate-50 overflow-hidden"
        dir="rtl"
    >
        <div
            class="absolute top-0 right-0 w-72 h-72 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"
        ></div>
        <div
            class="absolute top-0 -left-4 w-72 h-72 bg-sky-300 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"
        ></div>
        <div
            class="absolute -bottom-8 left-20 w-72 h-72 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000"
        ></div>

        <div
            class="relative z-10 w-full max-w-sm mx-4 p-8 bg-white/60 backdrop-blur-2xl border border-white/50 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] flex flex-col items-center text-center"
        >
            <div
                class="w-20 h-20 mb-6 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-3xl shadow-lg shadow-indigo-200 flex items-center justify-center rotate-3 transition-transform hover:rotate-0 duration-300"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="white"
                    class="w-10 h-10"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"
                    />
                </svg>
            </div>

            <h1 class="text-2xl font-black text-slate-800 mb-3 tracking-tight">
                سیستم مدیریت سفارشات
            </h1>
            <p class="text-sm text-slate-500 mb-8 leading-relaxed font-medium">
                دستیار هوشمند شما برای ثبت، پیگیری و مدیریت سفارشات فروشگاهی.
                سریع، دقیق و همیشه همراه شما در مسیر ویزیت و پرزنت.
            </p>

            <div class="w-full space-y-3" v-if="canLogin">
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="route('dashboard')"
                        class="block w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all active:scale-95"
                    >
                        ورود به داشبورد
                    </Link>
                </template>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="block w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all active:scale-95"
                    >
                        ورود به حساب کاربری
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="block w-full py-3.5 px-4 bg-white hover:bg-slate-50 text-indigo-600 border-2 border-indigo-100 hover:border-indigo-200 rounded-2xl font-bold transition-all active:scale-95"
                    >
                        ثبت‌نام پرزنتر جدید
                    </Link>
                </template>
            </div>

            <div class="mt-8 text-[10px] text-slate-400 font-semibold">
                طراحی شده برای سرعت و دقت در ثبت سفارش
            </div>
        </div>
    </div>
</template>

<style>
/* انیمیشن حرکت آرام دایره‌های نوری در پس‌زمینه */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}
.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
