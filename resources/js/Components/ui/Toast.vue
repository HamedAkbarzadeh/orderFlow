<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
    show: boolean;
    message: string;
    type?: "error" | "success" | "info" | "warning";
}>();

const typeConfig = computed(() => {
    switch (props.type) {
        case "success":
            return "bg-emerald-600/90 shadow-emerald-500/30";
        case "info":
            return "bg-blue-600/90 shadow-blue-500/30";
        case "warning":
            return "bg-amber-500/90 shadow-amber-500/30";
        default:
            return "bg-rose-600/90 shadow-rose-500/30"; // error
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 -translate-y-10"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed top-6 inset-x-0 flex justify-center z-50 px-4"
        >
            <div
                :class="typeConfig"
                class="backdrop-blur-xl border border-white/20 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3"
            >
                <span class="text-sm font-bold">{{ message }}</span>
            </div>
        </div>
    </Transition>
</template>
