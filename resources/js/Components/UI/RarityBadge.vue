<script setup lang="ts">
interface Props {
    rarity: number;
    size?: 'xs' | 'sm' | 'md' | 'lg';
    variant?: 'filled' | 'outlined';
}

const { rarity, size = 'sm', variant = 'filled' } = defineProps<Props>();

const rarityConfig = {
    1: { color: 'from-gray-400 to-gray-600', text: 'text-gray-100', border: 'border-gray-400' },
    2: { color: 'from-green-400 to-green-600', text: 'text-green-100', border: 'border-green-400' },
    3: { color: 'from-blue-400 to-blue-600', text: 'text-blue-100', border: 'border-blue-400' },
    4: { color: 'from-purple-400 to-purple-600', text: 'text-purple-100', border: 'border-purple-400' },
    5: { color: 'from-yellow-400 to-orange-500', text: 'text-yellow-100', border: 'border-yellow-400' },
    6: { color: 'from-pink-400 to-red-500', text: 'text-pink-100', border: 'border-pink-400' },
    7: { color: 'from-rainbow-start via-rainbow-mid to-rainbow-end', text: 'text-white', border: 'border-pink-500' }
};

const config = rarityConfig[rarity as keyof typeof rarityConfig] || rarityConfig[1];

const sizeClasses = {
    xs: 'px-1.5 py-0.5 text-xs gap-0.5',
    sm: 'px-2 py-1 text-xs gap-1',
    md: 'px-2.5 py-1.5 text-sm gap-1.5',
    lg: 'px-3 py-2 text-base gap-2'
};

const starSizeClasses = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg'
};

const baseClasses = variant === 'filled' 
    ? `bg-gradient-to-br ${config.color} ${config.text} shadow-lg`
    : `border-2 ${config.border} ${config.text} bg-transparent`;
</script>

<template>
    <span 
        :class="[
            'inline-flex items-center justify-center font-bold rounded-full transition-all duration-200 hover:scale-105',
            baseClasses,
            sizeClasses[size]
        ]"
    >
        <span 
            v-for="i in rarity" 
            :key="i"
            :class="['opacity-90', starSizeClasses[size]]"
        >
            ⭐
        </span>
    </span>
</template>

<style scoped>
/* Classes pour les gradients rainbow (rareté 7) */
.from-rainbow-start {
    --tw-gradient-from: #ff0000;
}
.via-rainbow-mid {
    --tw-gradient-to: transparent;
    --tw-gradient-stops: var(--tw-gradient-from), #ff8000 25%, #ffff00 50%, #00ff00 75%, var(--tw-gradient-to);
}
.to-rainbow-end {
    --tw-gradient-to: #0000ff;
}
</style> 