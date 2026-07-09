<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";

defineProps<{
    users: any[];
}>();

const toggleActivation = (userId: number) => {
    router.patch(
        route("admin.users.toggle-activation", userId),
        {},
        { preserveScroll: true },
    );
};
</script>

<template>
    <Head title="مدیریت پرزنترها" />
    <MainLayout>
        <div class="mb-6 mt-2 flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-black text-slate-800">پرزنترها 👥</h2>
                <p class="text-slate-500 text-sm mt-1">
                    فعال‌سازی و مدیریت حساب‌ها
                </p>
            </div>
        </div>

        <div class="space-y-3 pb-10">
            <div
                v-if="users.length === 0"
                class="text-center p-6 text-slate-500 bg-white/50 rounded-3xl"
            >
                هنوز کاربری ثبت نام نکرده است.
            </div>

            <div
                v-for="user in users"
                :key="user.id"
                class="bg-white/70 backdrop-blur-md border border-white/50 p-4 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] flex items-center justify-between"
            >
                <div>
                    <h3 class="font-bold text-slate-800 text-base">
                        {{ user.name }}
                    </h3>
                    <p
                        class="text-sm font-semibold text-slate-500 font-mono mt-1"
                        dir="ltr"
                    >
                        {{ user.phone }}
                    </p>
                </div>

                <button
                    @click="toggleActivation(user.id)"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all border active:scale-95"
                    :class="
                        user.activated_at
                            ? 'bg-emerald-50 text-emerald-600 border-emerald-200'
                            : 'bg-rose-50 text-rose-600 border-rose-200'
                    "
                >
                    <span
                        v-if="user.activated_at"
                        class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"
                    ></span>
                    <span
                        v-else
                        class="w-2 h-2 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.8)]"
                    ></span>
                    {{
                        user.activated_at
                            ? "فعال (مسدود کردن)"
                            : "مسدود (تایید کردن)"
                    }}
                </button>
            </div>
        </div>
    </MainLayout>
</template>
