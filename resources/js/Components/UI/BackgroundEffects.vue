<script setup lang="ts">
interface Props {
    backgroundImage?: string;
}

const { backgroundImage = '/images/section-me-background.jpg' } = defineProps<Props>();
</script>

<template>
    <div class="absolute inset-0">
        <div class="absolute inset-0 opacity-30">
            <img
                :src="backgroundImage"
                alt="Background"
                class="w-full h-full object-cover"
            />
        </div>

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-20 w-2 h-2 bg-primary/30 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
            <div class="absolute top-40 right-32 w-3 h-3 bg-secondary/20 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-32 left-16 w-2 h-2 bg-accent/25 rounded-full animate-bounce" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-20 right-20 w-3 h-3 bg-success/20 rounded-full animate-pulse" style="animation-delay: 3s;"></div>
        </div>
    </div>
</template>