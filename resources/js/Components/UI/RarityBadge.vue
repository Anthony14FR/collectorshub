<script setup lang="ts">
interface Props {
    rarity: string;
    size?: 'xs' | 'sm' | 'md' | 'lg';
    variant?: 'filled' | 'outlined';
}

const { rarity, size = 'sm', variant = 'filled' } = defineProps<Props>();

const sizeClasses = {
    xs: 'h-4 px-1.5 text-xs',
    sm: 'h-5 px-2 text-xs',
    md: 'h-6 px-3 text-sm',
    lg: 'h-7 px-3 text-base'
};

const getRarityConfig = (rarity: string) => {
    switch(rarity.toLowerCase()) {
        case 'normal':
            return {
                text: 'Normal',
                bgColor: 'bg-base-content/10',
                textColor: 'text-base-content/70',
                borderColor: 'border-base-content/20'
            };
        case 'rare':
            return {
                text: 'Rare',
                bgColor: 'bg-blue-500/20',
                textColor: 'text-blue-400',
                borderColor: 'border-blue-500/30'
            };
        case 'epic':
            return {
                text: 'Epic',
                bgColor: 'bg-purple-500/20',
                textColor: 'text-purple-400',
                borderColor: 'border-purple-500/30'
            };
        case 'legendary':
            return {
                text: 'Legendary',
                bgColor: 'bg-orange-500/20',
                textColor: 'text-orange-400',
                borderColor: 'border-orange-500/30'
            };
        default:
            return {
                text: 'Unknown',
                bgColor: 'bg-base-content/10',
                textColor: 'text-base-content/70',
                borderColor: 'border-base-content/20'
            };
    }
};

const config = getRarityConfig(rarity);
</script>

<template>
    <div 
        :class="[
            'w-auto backdrop-blur-sm rounded-full flex items-center justify-center border font-semibold uppercase tracking-wide',
            sizeClasses[size],
            config.bgColor,
            config.textColor,
            config.borderColor
        ]"
    >
        {{ config.text }}
    </div>
</template>

