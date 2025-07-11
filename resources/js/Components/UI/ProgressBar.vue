<script setup lang="ts">
interface Props {
    value: number;
    max: number;
    label: string;
}

const { value, max, label } = defineProps<Props>();

const percentage = (value / max) * 100;
</script>

<template>
    <div>
        <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-semibold text-base-content/80">{{ label }}</span>
            <span class="text-xs font-bold">{{ value }} / {{ max }}</span>
        </div>
        <div class="w-full bg-base-300/50 rounded-full h-2.5 overflow-hidden">
            <div 
                class="bg-gradient-to-r from-primary to-secondary h-2.5 rounded-full transition-all duration-500" 
                :style="{ width: percentage + '%' }"
            ></div>
        </div>
    </div>
</template> 