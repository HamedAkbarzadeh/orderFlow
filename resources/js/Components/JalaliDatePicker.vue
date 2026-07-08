<script setup lang="ts">
import { ref, computed } from "vue";
import moment from "moment-jalaali";
import {
    JALALI_MONTHS,
    daysInJalaliMonth,
    jalaliToGregorian,
    currentJalaliYear,
    toJalaliDisplay,
} from "@/utils/jalali";

// این کامپوننت مثل input type="date" کار می‌کند: مقدار ورودی/خروجی (modelValue)
// همیشه تاریخ میلادی با فرمت YYYY-MM-DD است تا بک‌اند لاراول نیازی به تغییر نداشته باشد.
// روی صفحه، یک دکمه نشون داده میشه که با کلیک روش، یک مدال با تقویم کامل شمسی باز میشه.

const props = defineProps<{
    modelValue: string | null;
    placeholder?: string;
}>();

const emit = defineEmits<{
    (e: "update:modelValue", value: string): void;
}>();

const isOpen = ref(false);

const weekDays = ["ش", "ی", "د", "س", "چ", "پ", "ج"];

const todayJalali = () => {
    const m = moment();
    return { y: m.jYear(), mo: m.jMonth() + 1, d: m.jDate() };
};

// تاریخ فعلاً انتخاب‌شده (برای هایلایت کردن روی تقویم)
const selected = computed(() => {
    if (props.modelValue) {
        const m = moment(props.modelValue, "YYYY-MM-DD");
        if (m.isValid()) {
            return { y: m.jYear(), mo: m.jMonth() + 1, d: m.jDate() };
        }
    }
    return null;
});

// ماه/سالی که تقویم روی آن باز است (وقتی کاربر جابه‌جا میشه، لزوماً همون انتخاب‌شده نیست)
const viewYear = ref<number>(todayJalali().y);
const viewMonth = ref<number>(todayJalali().mo);

const openPicker = () => {
    const base = selected.value ?? todayJalali();
    viewYear.value = base.y;
    viewMonth.value = base.mo;
    isOpen.value = true;
};

const closePicker = () => {
    isOpen.value = false;
};

const prevMonth = () => {
    if (viewMonth.value === 1) {
        viewMonth.value = 12;
        viewYear.value -= 1;
    } else {
        viewMonth.value -= 1;
    }
};

const nextMonth = () => {
    if (viewMonth.value === 12) {
        viewMonth.value = 1;
        viewYear.value += 1;
    } else {
        viewMonth.value += 1;
    }
};

// بازه سال‌ها برای انتخاب سریع: از ۵ سال قبل تا ۵ سال بعد از سال جاری
const years = Array.from({ length: 11 }, (_, i) => currentJalaliYear() - 5 + i);

const daysCount = computed(() =>
    daysInJalaliMonth(viewYear.value, viewMonth.value),
);

// شماره روز هفته اولین روز ماه (۰=شنبه ... ۶=جمعه) برای چیدمان درست خانه‌های خالی تقویم
const firstDayOffset = computed(() => {
    const m = moment(`${viewYear.value}/${viewMonth.value}/1`, "jYYYY/jM/jD");
    return (m.day() + 1) % 7;
});

const calendarCells = computed(() => {
    const cells: (number | null)[] = [];
    for (let i = 0; i < firstDayOffset.value; i++) cells.push(null);
    for (let d = 1; d <= daysCount.value; d++) cells.push(d);
    return cells;
});

const isSelectedDay = (d: number) =>
    !!selected.value &&
    selected.value.y === viewYear.value &&
    selected.value.mo === viewMonth.value &&
    selected.value.d === d;

const isToday = (d: number) => {
    const t = todayJalali();
    return t.y === viewYear.value && t.mo === viewMonth.value && t.d === d;
};

const selectDay = (d: number) => {
    const gregorian = jalaliToGregorian(viewYear.value, viewMonth.value, d);
    emit("update:modelValue", gregorian);
    isOpen.value = false;
};

const goToday = () => {
    const t = todayJalali();
    viewYear.value = t.y;
    viewMonth.value = t.mo;
    selectDay(t.d);
};

const displayValue = computed(() => toJalaliDisplay(props.modelValue));
</script>

<template>
    <div>
        <button
            type="button"
            @click="openPicker"
            class="w-full flex items-center justify-between bg-white/50 border border-slate-200 rounded-xl text-sm px-3 py-2.5"
        >
            <span
                :class="
                    modelValue
                        ? 'text-slate-700 font-semibold'
                        : 'text-slate-400'
                "
            >
                {{
                    modelValue ? displayValue : (placeholder ?? "انتخاب تاریخ")
                }}
            </span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                class="w-4 h-4 text-indigo-500 shrink-0"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                />
            </svg>
        </button>

        <Teleport to="body">
            <Transition name="jdp-fade">
                <div
                    v-if="isOpen"
                    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center"
                >
                    <div
                        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                        @click="closePicker"
                    ></div>

                    <div
                        class="relative bg-white w-full sm:w-80 sm:rounded-3xl rounded-t-3xl p-4 shadow-2xl"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <button
                                type="button"
                                @click="prevMonth"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-600 active:scale-90 transition-transform"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                    stroke="currentColor"
                                    class="w-4 h-4"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5"
                                    />
                                </svg>
                            </button>

                            <div class="flex items-center gap-2">
                                <span
                                    class="font-bold text-slate-800 text-sm"
                                    >{{ JALALI_MONTHS[viewMonth - 1] }}</span
                                >
                                <select
                                    v-model="viewYear"
                                    class="bg-slate-50 border-slate-200 rounded-lg text-xs font-bold text-indigo-600 py-1 pl-6"
                                >
                                    <option
                                        v-for="y in years"
                                        :key="y"
                                        :value="y"
                                    >
                                        {{ y }}
                                    </option>
                                </select>
                            </div>

                            <button
                                type="button"
                                @click="nextMonth"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-600 active:scale-90 transition-transform"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                    stroke="currentColor"
                                    class="w-4 h-4"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 19.5L8.25 12l7.5-7.5"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-7 gap-1 mb-1">
                            <span
                                v-for="wd in weekDays"
                                :key="wd"
                                class="text-[10px] font-bold text-slate-400 text-center py-1"
                                >{{ wd }}</span
                            >
                        </div>

                        <div class="grid grid-cols-7 gap-1">
                            <template
                                v-for="(cell, index) in calendarCells"
                                :key="index"
                            >
                                <button
                                    v-if="cell"
                                    type="button"
                                    @click="selectDay(cell)"
                                    class="aspect-square rounded-xl text-xs font-semibold flex items-center justify-center transition-colors"
                                    :class="[
                                        isSelectedDay(cell)
                                            ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200'
                                            : isToday(cell)
                                              ? 'bg-indigo-50 text-indigo-600 border border-indigo-200'
                                              : 'text-slate-600 hover:bg-slate-100',
                                    ]"
                                >
                                    {{ cell }}
                                </button>
                                <span v-else></span>
                            </template>
                        </div>

                        <button
                            type="button"
                            @click="goToday"
                            class="w-full mt-4 text-xs font-bold text-indigo-600 bg-indigo-50 py-2.5 rounded-xl active:scale-95 transition-transform"
                        >
                            رفتن به امروز
                        </button>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.jdp-fade-enter-active,
.jdp-fade-leave-active {
    transition: opacity 0.15s ease;
}
.jdp-fade-enter-from,
.jdp-fade-leave-to {
    opacity: 0;
}
</style>
