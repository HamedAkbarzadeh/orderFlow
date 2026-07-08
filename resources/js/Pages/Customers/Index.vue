<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import type { Customer } from './Customers.types';

const props = defineProps<{
    customers: Customer[];
}>();

// فرم ثبت مشتری جدید
const form = useForm({
    store_name: '',
    contact_name: '',
    phone: '',
    address: '',
});

const isFormOpen = ref(false);

const submitCustomer = () => {
    form.post(route('customers.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            isFormOpen.value = false;
        },
    });
};
</script>

<template>
    <Head title="مشتریان من" />

    <MainLayout>
        <div class="mb-6 mt-2 flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-black text-slate-800">مشتریان 👥</h2>
                <p class="text-slate-500 text-sm mt-1">مدیریت و ثبت فروشگاه‌های جدید</p>
            </div>

            <button
                @click="isFormOpen = !isFormOpen"
                class="bg-indigo-600 text-white w-10 h-10 rounded-full shadow-lg shadow-indigo-200 flex items-center justify-center transition-transform active:scale-90"
            >
                <svg v-if="!isFormOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div v-show="isFormOpen" class="bg-white/70 backdrop-blur-md border border-white/50 p-5 rounded-3xl shadow-sm mb-6 transition-all">
            <h3 class="font-bold text-slate-700 mb-4 text-sm flex items-center">
                <span class="w-2 h-2 rounded-full bg-indigo-500 ml-2"></span>
                ثبت فروشگاه جدید
            </h3>

            <form @submit.prevent="submitCustomer" class="space-y-3">
                <input type="text" v-model="form.store_name" placeholder="نام فروشگاه (مثلا سوپر مارکت یاران)" required class="w-full bg-white/50 border-slate-200 rounded-xl text-sm" />
                <input type="text" v-model="form.contact_name" placeholder="نام شخص مسئول (مثلا آقای اصغری)" required class="w-full bg-white/50 border-slate-200 rounded-xl text-sm" />
                <input type="tel" v-model="form.phone" placeholder="شماره تماس" dir="ltr" required class="w-full bg-white/50 border-slate-200 rounded-xl text-sm text-right" />
                <textarea v-model="form.address" placeholder="آدرس دقیق مغازه..." required rows="2" class="w-full bg-white/50 border-slate-200 rounded-xl text-sm resize-none"></textarea>

                <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 disabled:bg-slate-400 text-white py-3 rounded-xl text-sm font-bold shadow-md">
                    {{ form.processing ? 'در حال ثبت...' : 'ذخیره مشتری' }}
                </button>
            </form>
        </div>

        <div class="space-y-3 pb-10">
            <div v-if="customers.length === 0" class="text-center p-6 text-slate-500 bg-white/50 rounded-3xl">
                هیچ مشتری ثبت نشده است.
            </div>

            <div
                v-for="customer in customers"
                :key="customer.id"
                class="bg-white/70 backdrop-blur-md border border-white/50 p-4 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] flex flex-col gap-2"
            >
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">{{ customer.store_name }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ customer.contact_name }}</p>
                    </div>
                    <a :href="'tel:' + customer.phone" class="bg-emerald-50 text-emerald-600 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                    </a>
                </div>
                <div class="text-xs text-slate-600 bg-slate-50/50 p-2 rounded-xl mt-1 border border-slate-100 flex gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    {{ customer.address }}
                </div>
            </div>
        </div>
    </MainLayout>
</template>
