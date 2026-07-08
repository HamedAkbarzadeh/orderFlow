<script setup lang="ts">
import { ref } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import CurrencyInput from "@/Components/ui/CurrencyInput.vue";
import type { Product, ProductAttribute } from "./Products.types";

const props = defineProps<{
    products: Product[];
}>();

const isFormOpen = ref(false);
const isEditing = ref(false);
const editingProductId = ref<number | null>(null);

const form = useForm({
    name: "",
    price: "" as number | string,
    attributes: [] as ProductAttribute[],
});

// باز کردن فرم برای ثبت محصول جدید
const openCreateForm = () => {
    isEditing.value = false;
    editingProductId.value = null;
    form.reset();
    isFormOpen.value = true;
};

// باز کردن فرم برای ویرایش محصول موجود
const openEditForm = (product: Product) => {
    isEditing.value = true;
    editingProductId.value = product.id;
    form.name = product.name;
    form.price = product.price;
    // یک کپی عمیق از ویژگی‌ها می‌گیریم تا قبل از ذخیره، روی لیست اصلی تاثیر نگذارد
    form.attributes = JSON.parse(JSON.stringify(product.attributes));
    isFormOpen.value = true;
    window.scrollTo({ top: 0, behavior: "smooth" });
};

const addAttribute = () => {
    form.attributes.push({
        key: "",
        value: "",
        unit: "",
        price_increase: 0,
    });
};

const removeAttribute = (index: number) => {
    form.attributes.splice(index, 1);
};

// ارسال فرم (تشخیص اتوماتیک بین ثبت جدید و ویرایش)
const submitProduct = () => {
    if (isEditing.value && editingProductId.value) {
        form.put(route("products.update", editingProductId.value), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                isFormOpen.value = false;
                isEditing.value = false;
            },
        });
    } else {
        form.post(route("products.store"), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                isFormOpen.value = false;
            },
        });
    }
};

// حذف محصول
const deleteProduct = (productId: number) => {
    if (
        confirm(
            "آیا از حذف این محصول اطمینان دارید؟ تمام ویژگی‌های آن نیز پاک خواهند شد.",
        )
    ) {
        router.delete(route("products.destroy", productId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="محصولات" />

    <MainLayout>
        <div class="mb-6 mt-2 flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-black text-slate-800">محصولات 📦</h2>
                <p class="text-slate-500 text-sm mt-1">
                    مدیریت محصولات و ویژگی‌ها
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
                {{ isEditing ? "ویرایش محصول" : "تعریف محصول جدید" }}
            </h3>

            <form @submit.prevent="submitProduct" class="space-y-4">
                <div class="flex gap-2">
                    <input
                        type="text"
                        v-model="form.name"
                        placeholder="نام محصول (مثلاً موبایل)"
                        required
                        class="w-1/2 bg-white/50 border-slate-200 rounded-xl text-sm"
                    />
                    <CurrencyInput
                        v-model="form.price"
                        placeholder="قیمت پایه"
                        required
                        class="w-1/2 bg-white/50 border-slate-200 rounded-xl text-sm"
                    />
                </div>

                <div
                    class="bg-indigo-50/50 p-3 rounded-2xl border border-indigo-100"
                >
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-indigo-700"
                            >ویژگی‌های متغیر (اختیاری)</label
                        >
                        <button
                            type="button"
                            @click="addAttribute"
                            class="text-xs bg-indigo-200 text-indigo-700 px-2 py-1 rounded-lg font-bold"
                        >
                            + افزودن
                        </button>
                    </div>

                    <div
                        v-for="(attr, index) in form.attributes"
                        :key="index"
                        class="flex gap-2 mb-2 items-start"
                    >
                        <div class="w-full space-y-2">
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    v-model="attr.key"
                                    placeholder="نام (مثلا رم)"
                                    required
                                    class="w-1/2 bg-white border-slate-200 rounded-xl text-xs py-1.5"
                                />
                                <input
                                    type="text"
                                    v-model="attr.value"
                                    placeholder="مقدار (مثلا ۱۶)"
                                    required
                                    class="w-1/2 bg-white border-slate-200 rounded-xl text-xs py-1.5"
                                />
                            </div>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    v-model="attr.unit"
                                    placeholder="واحد (مثلا گیگ)"
                                    class="w-1/2 bg-white border-slate-200 rounded-xl text-xs py-1.5"
                                />
                                <CurrencyInput
                                    v-model="attr.price_increase"
                                    placeholder="افزایش قیمت"
                                    required
                                    class="w-1/2 bg-white border-slate-200 rounded-xl text-xs py-1.5"
                                />
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="removeAttribute(index)"
                            class="bg-red-100 text-red-500 w-8 h-8 flex shrink-0 items-center justify-center rounded-xl mt-1"
                        >
                            ✕
                        </button>
                    </div>
                    <p
                        v-if="form.attributes.length === 0"
                        class="text-xs text-slate-400 text-center py-2"
                    >
                        هیچ ویژگی ثبت نشده است.
                    </p>
                </div>

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
                              : "ذخیره محصول"
                    }}
                </button>
            </form>
        </div>

        <div class="space-y-3 pb-10">
            <div
                v-if="products.length === 0"
                class="text-center p-6 text-slate-500 bg-white/50 rounded-3xl"
            >
                هیچ محصولی ثبت نشده است.
            </div>

            <div
                v-for="product in products"
                :key="product.id"
                class="bg-white/70 backdrop-blur-md border border-white/50 p-4 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)]"
            >
                <div
                    class="flex justify-between items-center border-b border-slate-100 pb-2 mb-2"
                >
                    <h3 class="font-bold text-slate-800 text-base">
                        {{ product.name }}
                    </h3>
                    <span
                        class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg"
                    >
                        پایه: {{ Number(product.price).toLocaleString() }} ریال
                    </span>
                </div>

                <div
                    v-if="product.attributes.length > 0"
                    class="space-y-1 mb-3"
                >
                    <p class="text-[10px] text-slate-400 font-bold mb-1">
                        ویژگی‌ها:
                    </p>
                    <div
                        v-for="attr in product.attributes"
                        :key="attr.id"
                        class="flex justify-between items-center text-xs text-slate-600 bg-slate-50 p-1.5 rounded-lg border border-slate-100"
                    >
                        <span
                            >{{ attr.key }}: {{ attr.value }}
                            {{ attr.unit }}</span
                        >
                        <span class="font-semibold text-emerald-600"
                            >+{{
                                Number(attr.price_increase).toLocaleString()
                            }}
                            ریال</span
                        >
                    </div>
                </div>

                <div
                    class="flex justify-end gap-2 pt-2 border-t border-slate-100"
                >
                    <button
                        @click="openEditForm(product)"
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
                        @click="deleteProduct(product.id)"
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
