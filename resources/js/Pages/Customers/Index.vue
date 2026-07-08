<script setup lang="ts">
import { ref } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import type { Customer } from "./Customers.types";

const props = defineProps<{
    customers: Customer[];
}>();

const isFormOpen = ref(false);
const isEditing = ref(false);
const editingCustomerId = ref<number | null>(null);

// فرم ثبت و ویرایش مشتری
const form = useForm({
    store_name: "",
    contact_name: "",
    phone: "",
    address: "",
});

// باز کردن فرم برای ثبت مشتری جدید
const openCreateForm = () => {
    isEditing.value = false;
    editingCustomerId.value = null;
    form.reset();
    isFormOpen.value = true;
};

// باز کردن فرم برای ویرایش مشتری موجود
const openEditForm = (customer: Customer) => {
    isEditing.value = true;
    editingCustomerId.value = customer.id;
    form.store_name = customer.store_name;
    form.contact_name = customer.contact_name;
    form.phone = customer.phone;
    form.address = customer.address;
    isFormOpen.value = true;
    window.scrollTo({ top: 0, behavior: "smooth" });
};

// ارسال فرم (تشخیص بین ثبت جدید و ویرایش)
const submitCustomer = () => {
    if (isEditing.value && editingCustomerId.value) {
        form.put(route("customers.update", editingCustomerId.value), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                isFormOpen.value = false;
                isEditing.value = false;
            },
        });
    } else {
        form.post(route("customers.store"), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                isFormOpen.value = false;
            },
        });
    }
};

// حذف مشتری
const deleteCustomer = (customerId: number) => {
    if (confirm("آیا از حذف این مشتری اطمینان دارید؟")) {
        router.delete(route("customers.destroy", customerId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="مشتریان من" />

    <MainLayout>
        <div class="mb-6 mt-2 flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-black text-slate-800">مشتریان 👥</h2>
                <p class="text-slate-500 text-sm mt-1">
                    مدیریت و ثبت فروشگاه‌های جدید
                </p>
            </div>

            <button
                @click="isFormOpen ? (isFormOpen = false) : openCreateForm()"
                class="bg-indigo-600 text-white w-10 h-10 rounded-full shadow-lg shadow-indigo-200 flex items-center justify-center transition-transform active:scale-90"
            >
                <svg
                    v-if="!isFormOpen"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2.5"
                    stroke="currentColor"
                    class="w-6 h-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4.5v15m7.5-7.5h-15"
                    />
                </svg>
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2.5"
                    stroke="currentColor"
                    class="w-6 h-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>

        <div
            v-show="isFormOpen"
            class="bg-white/70 backdrop-blur-md border border-white/50 p-5 rounded-3xl shadow-sm mb-6 transition-all"
        >
            <h3 class="font-bold text-slate-700 mb-4 text-sm flex items-center">
                <span
                    class="w-2 h-2 rounded-full ml-2"
                    :class="isEditing ? 'bg-amber-500' : 'bg-indigo-500'"
                ></span>
                {{ isEditing ? "ویرایش فروشگاه" : "ثبت فروشگاه جدید" }}
            </h3>

            <form @submit.prevent="submitCustomer" class="space-y-3">
                <input
                    type="text"
                    v-model="form.store_name"
                    placeholder="نام فروشگاه (مثلا سوپر مارکت یاران)"
                    required
                    class="w-full bg-white/50 border-slate-200 rounded-xl text-sm"
                />
                <input
                    type="text"
                    v-model="form.contact_name"
                    placeholder="نام شخص مسئول (مثلا آقای اصغری)"
                    required
                    class="w-full bg-white/50 border-slate-200 rounded-xl text-sm"
                />
                <input
                    type="tel"
                    v-model="form.phone"
                    placeholder="شماره تماس"
                    dir="ltr"
                    required
                    class="w-full bg-white/50 border-slate-200 rounded-xl text-sm text-right"
                />
                <textarea
                    v-model="form.address"
                    placeholder="آدرس دقیق مغازه..."
                    required
                    rows="2"
                    class="w-full bg-white/50 border-slate-200 rounded-xl text-sm resize-none"
                ></textarea>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full disabled:bg-slate-400 text-white py-3 rounded-xl text-sm font-bold shadow-md transition-colors"
                    :class="
                        isEditing
                            ? 'bg-amber-500 hover:bg-amber-600'
                            : 'bg-indigo-600 hover:bg-indigo-700'
                    "
                >
                    {{
                        form.processing
                            ? "در حال ذخیره..."
                            : isEditing
                              ? "ثبت تغییرات"
                              : "ذخیره مشتری"
                    }}
                </button>
            </form>
        </div>

        <div class="space-y-3 pb-10">
            <div
                v-if="customers.length === 0"
                class="text-center p-6 text-slate-500 bg-white/50 rounded-3xl"
            >
                هیچ مشتری ثبت نشده است.
            </div>

            <div
                v-for="customer in customers"
                :key="customer.id"
                class="bg-white/70 backdrop-blur-md border border-white/50 p-4 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] flex flex-col gap-2"
            >
                <div
                    class="flex justify-between items-start border-b border-slate-100 pb-2"
                >
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">
                            {{ customer.store_name }}
                        </h3>
                        <p class="text-xs text-slate-500 font-medium">
                            {{ customer.contact_name }}
                        </p>
                    </div>
                    <a
                        :href="'tel:' + customer.phone"
                        class="bg-emerald-50 text-emerald-600 p-2 rounded-full hover:bg-emerald-100 transition-colors"
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
                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"
                            />
                        </svg>
                    </a>
                </div>
                <div
                    class="text-xs text-slate-600 bg-slate-50/50 p-2 rounded-xl border border-slate-100 flex gap-1 mb-1"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-4 h-4 text-slate-400 shrink-0"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                        />
                    </svg>
                    {{ customer.address }}
                </div>

                <div class="flex justify-end gap-2 pt-1">
                    <button
                        @click="openEditForm(customer)"
                        class="flex items-center gap-1 bg-amber-50 text-amber-600 px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-amber-100 transition-colors"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-3.5 h-3.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                            />
                        </svg>
                        ویرایش
                    </button>
                    <button
                        @click="deleteCustomer(customer.id)"
                        class="flex items-center gap-1 bg-rose-50 text-rose-600 px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-rose-100 transition-colors"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-3.5 h-3.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                            />
                        </svg>
                        حذف
                    </button>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
