<script setup lang="ts">
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    phone: "",
    password: "",
    remember: false,
});

// متغیرهای مربوط به نمایش Toast
const showToast = ref(false);
const toastMessage = ref("");

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
        onError: (errors) => {
            // اگر ارور مربوط به شماره موبایل بود و کلمه "پشتیبانی" در آن وجود داشت
            if (errors.phone && errors.phone.includes("پشتیبانی")) {
                toastMessage.value = errors.phone;
                showToast.value = true;

                // پاک کردن ارور از زیر اینپوت
                form.clearErrors("phone");

                // مخفی کردن Toast بعد از 5 ثانیه
                setTimeout(() => {
                    showToast.value = false;
                }, 5000);
            }
        },
    });
};
</script>

<template>
    <Head title="ورود به سیستم" />

    <div
        class="relative min-h-screen flex items-center justify-center bg-slate-50 overflow-hidden"
        dir="rtl"
    >
        <Transition
            enter-active-class="transition ease-out duration-300 transform"
            enter-from-class="opacity-0 -translate-y-10 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition ease-in duration-200 transform"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 -translate-y-10 scale-95"
        >
            <div
                v-if="showToast"
                class="fixed top-6 inset-x-0 flex justify-center z-50 px-4"
            >
                <div
                    class="bg-rose-600/90 backdrop-blur-xl border border-rose-400/50 text-white px-5 py-3 rounded-2xl shadow-[0_8px_30px_rgb(225,29,72,0.3)] flex items-center gap-3"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                    <span class="text-sm font-bold">{{ toastMessage }}</span>
                </div>
            </div>
        </Transition>

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
            class="relative z-10 w-full max-w-sm mx-4 p-8 bg-white/60 backdrop-blur-2xl border border-white/50 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)]"
        >
            <div class="text-center mb-8">
                <div
                    class="w-16 h-16 mx-auto mb-4 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center rotate-3 shadow-sm"
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
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"
                        />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                    خوش برگشتید!
                </h2>
                <p class="text-sm text-slate-500 mt-1 font-medium">
                    شماره موبایل و رمز عبور خود را وارد کنید
                </p>
            </div>

            <div
                v-if="status"
                class="mb-4 font-medium text-sm text-emerald-600 text-center bg-emerald-50 p-2 rounded-xl"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
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
                        autofocus
                        placeholder="0912..."
                    />
                    <p
                        v-if="
                            form.errors.phone &&
                            !form.errors.phone.includes('پشتیبانی')
                        "
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

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            v-model="form.remember"
                            class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        />
                        <span class="mr-2 text-xs font-medium text-slate-600"
                            >مرا به خاطر بسپار</span
                        >
                    </label>
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
                            form.processing ? "در حال ورود..." : "ورود به حساب"
                        }}
                    </button>
                </div>

                <div class="text-center pt-4 border-t border-slate-200/60 mt-4">
                    <p class="text-xs font-medium text-slate-500">
                        حساب کاربری ندارید؟
                        <Link
                            :href="route('register')"
                            class="text-indigo-600 hover:text-indigo-700 font-bold ml-1"
                            >ثبت‌نام کنید</Link
                        >
                    </p>
                </div>
            </form>
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
