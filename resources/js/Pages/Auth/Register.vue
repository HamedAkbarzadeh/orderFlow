<script setup lang="ts">
import { ref } from "vue"; // اضافه شدن ref
import { Head, Link, useForm } from "@inertiajs/vue3";
import Toast from "@/Components/ui/Toast.vue"; // ایمپورت کامپوننت Toast

const form = useForm({
    name: "",
    phone: "",
    password: "",
    password_confirmation: "",
});

// متغیرهای توست
const showToast = ref(false);
const toastMessage = ref("");

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
        onError: (errors) => {
            // شکار ارور و نمایش توست
            if (errors.phone && errors.phone.includes("پشتیبانی")) {
                toastMessage.value = errors.phone;
                showToast.value = true;
                form.clearErrors("phone");
                setTimeout(() => {
                    showToast.value = false;
                }, 5000);
            }
        },
    });
};
</script>

<template>
    <Head title="ثبت‌نام پرزنتر" />

    <div
        class="relative min-h-screen flex flex-col items-center justify-center bg-slate-50 overflow-hidden"
        dir="rtl"
    >
        <Toast :show="showToast" :message="toastMessage" />

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
            class="relative z-10 w-full max-w-sm mx-4 p-8 bg-white/60 backdrop-blur-2xl border border-white/50 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] my-8"
        >
            <div class="text-center mb-8">
                <div
                    class="w-16 h-16 mx-auto mb-4 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center -rotate-3 shadow-sm"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-8 h-8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.66-1.546"
                        />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                    ایجاد حساب جدید
                </h2>
                <p class="text-sm text-slate-500 mt-1 font-medium">
                    مشخصات خود را برای ثبت‌نام وارد کنید
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label
                        for="name"
                        class="block text-xs font-bold text-slate-700 mb-1.5 pr-1"
                        >نام و نام خانوادگی</label
                    >
                    <input
                        id="name"
                        type="text"
                        class="w-full bg-white/50 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl text-sm placeholder-slate-400 transition-all"
                        v-model="form.name"
                        required
                        autofocus
                        placeholder="مثلا: علی محمدی"
                    />
                    <p
                        v-if="form.errors.name"
                        class="mt-1.5 text-xs text-rose-500 pr-1"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <div>
                    <label
                        for="phone"
                        class="block text-xs font-bold text-slate-700 mb-1.5 pr-1"
                        >شماره موبایل</label
                    >
                    <input
                        id="phone"
                        type="tel"
                        dir="ltr"
                        class="w-full bg-white/50 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl text-sm placeholder-slate-400 text-left transition-all"
                        v-model="form.phone"
                        required
                        placeholder="0912..."
                    />
                    <p
                        v-if="form.errors.phone"
                        class="mt-1.5 text-xs text-rose-500 pr-1"
                    >
                        {{ form.errors.phone }}
                    </p>
                </div>

                <div>
                    <label
                        for="password"
                        class="block text-xs font-bold text-slate-700 mb-1.5 pr-1"
                        >رمز عبور</label
                    >
                    <input
                        id="password"
                        type="password"
                        dir="ltr"
                        class="w-full bg-white/50 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl text-sm placeholder-slate-400 text-left transition-all"
                        v-model="form.password"
                        required
                        placeholder="••••••••"
                    />
                    <p
                        v-if="form.errors.password"
                        class="mt-1.5 text-xs text-rose-500 pr-1"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="block text-xs font-bold text-slate-700 mb-1.5 pr-1"
                        >تکرار رمز عبور</label
                    >
                    <input
                        id="password_confirmation"
                        type="password"
                        dir="ltr"
                        class="w-full bg-white/50 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl text-sm placeholder-slate-400 text-left transition-all"
                        v-model="form.password_confirmation"
                        required
                        placeholder="••••••••"
                    />
                    <p
                        v-if="form.errors.password_confirmation"
                        class="mt-1.5 text-xs text-rose-500 pr-1"
                    >
                        {{ form.errors.password_confirmation }}
                    </p>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all active:scale-95 flex justify-center items-center"
                        :class="{
                            'opacity-70 cursor-not-allowed': form.processing,
                        }"
                        :disabled="form.processing"
                    >
                        <svg
                            v-if="form.processing"
                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        {{
                            form.processing
                                ? "در حال ثبت‌نام..."
                                : "ثبت‌نام پرزنتر"
                        }}
                    </button>
                </div>

                <div class="text-center pt-4 border-t border-slate-200/60 mt-4">
                    <p class="text-xs font-medium text-slate-500">
                        قبلاً ثبت‌نام کرده‌اید؟
                        <Link
                            :href="route('login')"
                            class="text-indigo-600 hover:text-indigo-700 font-bold ml-1"
                            >وارد شوید</Link
                        >
                    </p>
                </div>
            </form>
        </div>
        <div
            class="bg-white mt-6 text-center text-xs font-semibold text-slate-500 bg-slate-100/50 p-3 rounded-xl border border-slate-200"
        >
            جهت ارتباط با پشتیبانی به این شماره تماس حاصل کنید:<br />
            <a
                href="tel:09379674614"
                class="text-indigo-600 font-black text-sm mt-1 inline-block"
                dir="ltr"
                >0937 967 4614</a
            >
        </div>
    </div>
</template>

<style>
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
