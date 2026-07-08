<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import JalaliDatePicker from "@/Components/JalaliDatePicker.vue";
import { PAYMENT_TYPES } from "./Orders.constants";
import type { Customer, Product, ProductAttribute } from "./Orders.types";
import CurrencyInput from "@/Components/ui/CurrencyInput.vue";

// دریافت دیتاهای پاس داده شده از کنترلر
const props = defineProps<{
    customers: Customer[];
    products: Product[];
}>();

// فرم ثبت سفارش با استفاده از Inertia
const form = useForm({
    customer_id: "",
    delivery_date: "",
    payment_type: PAYMENT_TYPES.CASH,
    installment_date: "",
    discount: 0,
    items: [] as Array<{
        product_id: number;
        product_attribute_id: number | null;
        quantity: number;
        unit_price: number;
        product_name?: string;
        attribute_name?: string;
    }>,
});

// استیت‌های مربوط به انتخاب محصول و افزودن به سبد خرید
const selectedProductId = ref<number | "">("");
const selectedAttributeId = ref<number | "">("");
const selectedQuantity = ref<number>(1);

// محاسبه محصول انتخاب شده برای نمایش ویژگی‌ها
const selectedProduct = computed(() => {
    return props.products.find((p) => p.id === selectedProductId.value) || null;
});

// محاسبه قیمت واحد محصول انتخاب شده با احتساب ویژگی‌ها
const currentUnitPrice = computed(() => {
    if (!selectedProduct.value) return 0;
    let price = selectedProduct.value.price;
    if (selectedAttributeId.value) {
        const attr = selectedProduct.value.attributes.find(
            (a) => a.id === selectedAttributeId.value,
        );
        if (attr) price += attr.price_increase;
    }
    return price;
});

// تابع افزودن محصول به لیست اقلام سفارش (سبد خرید)
const addItemToCart = () => {
    if (!selectedProduct.value || selectedQuantity.value < 1) return;

    const attr = selectedProduct.value.attributes.find(
        (a) => a.id === selectedAttributeId.value,
    );

    form.items.push({
        product_id: selectedProduct.value.id,
        product_attribute_id:
            selectedAttributeId.value === ""
                ? null
                : (selectedAttributeId.value as number),
        quantity: selectedQuantity.value,
        unit_price: currentUnitPrice.value,
        product_name: selectedProduct.value.name,
        attribute_name: attr
            ? `${attr.key}: ${attr.value} ${attr.unit || ""}`
            : "",
    });

    // ریست کردن فرم انتخاب محصول
    selectedProductId.value = "";
    selectedAttributeId.value = "";
    selectedQuantity.value = 1;
};

// حذف یک قلم از سبد خرید
const removeItem = (index: number) => {
    form.items.splice(index, 1);
};

// محاسبه جمع کل فاکتور
const cartTotal = computed(() => {
    const total = form.items.reduce(
        (sum, item) => sum + item.unit_price * item.quantity,
        0,
    );
    return Math.max(0, total - form.discount);
});

// وقتی نوع پرداخت از «قسطی» به چیز دیگه‌ای تغییر کنه، تاریخ قسط ثبت‌شده پاک میشه
watch(
    () => form.payment_type,
    (newType) => {
        if (newType !== PAYMENT_TYPES.INSTALLMENT_PLAN) {
            form.installment_date = "";
        }
    },
);

// ارسال فرم به سمت بک‌اند
const submitOrder = () => {
    form.post(route("orders.store"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="ثبت سفارش جدید" />

    <MainLayout>
        <div class="mb-6 mt-2">
            <h2 class="text-2xl font-black text-slate-800">سفارش جدید 📝</h2>
            <p class="text-slate-500 text-sm mt-1">
                اطلاعات فاکتور را با دقت وارد کنید
            </p>
        </div>

        <form @submit.prevent="submitOrder" class="space-y-6 pb-10">
            <div
                class="bg-white/70 backdrop-blur-md border border-white/50 p-5 rounded-3xl shadow-sm"
            >
                <h3
                    class="font-bold text-slate-700 mb-3 text-sm flex items-center"
                >
                    <span
                        class="w-2 h-2 rounded-full bg-indigo-500 ml-2"
                    ></span>
                    انتخاب مشتری
                </h3>
                <select
                    v-model="form.customer_id"
                    required
                    class="w-full bg-white/50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="" disabled>یک فروشگاه انتخاب کنید...</option>
                    <option
                        v-for="customer in customers"
                        :key="customer.id"
                        :value="customer.id"
                    >
                        {{ customer.store_name }} ({{ customer.contact_name }})
                    </option>
                </select>
                <button
                    type="button"
                    class="mt-2 text-xs font-semibold text-indigo-600 flex items-center"
                >
                    + ثبت مشتری جدید در همینجا
                </button>
            </div>

            <div
                class="bg-white/70 backdrop-blur-md border border-white/50 p-5 rounded-3xl shadow-sm"
            >
                <h3
                    class="font-bold text-slate-700 mb-3 text-sm flex items-center"
                >
                    <span
                        class="w-2 h-2 rounded-full bg-emerald-500 ml-2"
                    ></span>
                    افزودن محصول
                </h3>

                <div class="space-y-3">
                    <select
                        v-model="selectedProductId"
                        @change="selectedAttributeId = ''"
                        class="w-full bg-white/50 border-slate-200 rounded-xl text-sm"
                    >
                        <option value="" disabled>انتخاب محصول...</option>
                        <option
                            v-for="product in products"
                            :key="product.id"
                            :value="product.id"
                        >
                            {{ product.name }} - پایه:
                            {{ product.price.toLocaleString() }}
                        </option>
                    </select>

                    <select
                        v-if="
                            selectedProduct &&
                            selectedProduct.attributes.length > 0
                        "
                        v-model="selectedAttributeId"
                        class="w-full bg-white/50 border-slate-200 rounded-xl text-sm"
                    >
                        <option value="" disabled>
                            انتخاب ویژگی (اختیاری)...
                        </option>
                        <option
                            v-for="attr in selectedProduct.attributes"
                            :key="attr.id"
                            :value="attr.id"
                        >
                            {{ attr.key }}: {{ attr.value }}
                            {{ attr.unit }} (+{{
                                attr.price_increase.toLocaleString()
                            }})
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <input
                            type="number"
                            v-model="selectedQuantity"
                            min="1"
                            placeholder="تعداد"
                            class="w-1/3 bg-white/50 border-slate-200 rounded-xl text-sm text-center"
                        />
                        <button
                            type="button"
                            @click="addItemToCart"
                            :disabled="!selectedProductId"
                            class="w-2/3 bg-indigo-600 disabled:bg-slate-300 text-white rounded-xl text-sm font-bold shadow-md transition-colors"
                        >
                            اضافه کردن به لیست
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-if="form.items.length > 0"
                class="bg-white/70 backdrop-blur-md border border-white/50 p-5 rounded-3xl shadow-sm"
            >
                <h3
                    class="font-bold text-slate-700 mb-3 text-sm flex items-center"
                >
                    <span class="w-2 h-2 rounded-full bg-amber-500 ml-2"></span>
                    لیست سفارشات
                </h3>

                <div class="space-y-2">
                    <div
                        v-for="(item, index) in form.items"
                        :key="index"
                        class="flex justify-between items-center bg-white p-3 rounded-2xl border border-slate-100 shadow-sm"
                    >
                        <div>
                            <p class="font-bold text-sm text-slate-800">
                                {{ item.product_name }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ item.attribute_name }}
                            </p>
                            <p
                                class="text-xs font-semibold text-indigo-600 mt-1"
                            >
                                {{ item.quantity }} عدد x
                                {{ item.unit_price.toLocaleString() }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="removeItem(index)"
                            class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-full"
                        >
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="bg-white/70 backdrop-blur-md border border-white/50 p-5 rounded-3xl shadow-sm space-y-4"
            >
                <h3
                    class="font-bold text-slate-700 mb-1 text-sm flex items-center"
                >
                    <span class="w-2 h-2 rounded-full bg-blue-500 ml-2"></span>
                    اطلاعات نهایی و پرداخت
                </h3>

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-600 mb-1"
                        >تاریخ تحویل</label
                    >
                    <JalaliDatePicker v-model="form.delivery_date" />
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-600 mb-1"
                        >نوع پرداخت</label
                    >
                    <select
                        v-model="form.payment_type"
                        required
                        class="w-full bg-white/50 border-slate-200 rounded-xl text-sm"
                    >
                        <option
                            v-for="(label, key) in PAYMENT_TYPES"
                            :key="key"
                            :value="label"
                        >
                            {{ label }}
                        </option>
                    </select>
                </div>

                <div
                    v-if="form.payment_type === PAYMENT_TYPES.INSTALLMENT_PLAN"
                >
                    <label
                        class="block text-xs font-semibold text-slate-600 mb-1"
                        >تاریخ سررسید قسط</label
                    >
                    <JalaliDatePicker v-model="form.installment_date" />
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-600 mb-1"
                        >تخفیف دستی پرزنتر</label
                    >
                    <CurrencyInput
                        v-model="form.discount"
                        class="bg-white/50 border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>

                <div
                    class="pt-3 border-t border-slate-200 flex justify-between items-center"
                >
                    <span class="font-bold text-slate-600">مبلغ نهایی:</span>
                    <span class="font-black text-lg text-indigo-700"
                        >{{ cartTotal.toLocaleString() }} ریال</span
                    >
                </div>
            </div>

            <button
                type="submit"
                :disabled="form.processing || form.items.length === 0"
                class="w-full bg-slate-800 disabled:bg-slate-400 text-white py-4 rounded-2xl text-base font-bold shadow-lg shadow-slate-300 transition-transform active:scale-95 flex justify-center items-center"
            >
                ثبت نهایی سفارش
            </button>
        </form>
    </MainLayout>
</template>
