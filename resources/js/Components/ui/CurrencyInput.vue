<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    modelValue: number | string;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    class?: string;
}>();

const emit = defineEmits(['update:modelValue']);

// تبدیل اعداد فارسی به انگلیسی برای پردازش صحیح
const toEnglishDigits = (str: string) => {
    const persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g];
    let result = str;
    for (let i = 0; i < 10; i++) {
        result = result.replace(persianNumbers[i], i.toString());
    }
    return result;
};

const displayValue = computed({
    get: () => {
        if (!props.modelValue && props.modelValue !== 0) return '';
        // فرمت کردن عدد با جداکننده کاما
        return Number(props.modelValue).toLocaleString('en-US');
    },
    set: (val: string) => {
        let englishVal = toEnglishDigits(val);
        // حذف تمام کاراکترهای غیر عددی
        const rawValue = englishVal.replace(/\D/g, '');
        // ارسال عدد خام (بدون کاما) به v-model
        emit('update:modelValue', rawValue ? parseInt(rawValue, 10) : '');
    }
});
</script>

<template>
    <div class="relative w-full">
        <input
            type="text"
            inputmode="numeric"
            dir="ltr"
            v-model="displayValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            class="w-full pl-12 text-right"
            :class="class"
        />
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">ریال</span>
    </div>
</template>
