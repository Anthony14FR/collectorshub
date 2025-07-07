<script setup lang="ts">
interface Props {
    rarity: string;
    size?: 'xs' | 'sm' | 'md' | 'lg';
    variant?: 'filled' | 'outlined' | 'minimal';
    showText?: boolean;
}

const { rarity, size = 'sm', variant = 'filled', showText = true } = defineProps<Props>();

const rarityConfig = {
    normal: {
        color: 'from-slate-400 to-slate-600',
        text: 'text-slate-100',
        border: 'border-slate-400',
        bgColor: 'bg-slate-500/20',
        icon: '⚪',
        stars: 1,
        name: 'Normal',
        glow: 'shadow-slate-400/30'
    },
    rare: {
        color: 'from-blue-400 to-blue-600',
        text: 'text-blue-100',
        border: 'border-blue-400',
        bgColor: 'bg-blue-500/20',
        icon: '🔵',
        stars: 2,
        name: 'Rare',
        glow: 'shadow-blue-400/30'
    },
    epic: {
        color: 'from-purple-400 to-purple-600',
        text: 'text-purple-100',
        border: 'border-purple-400',
        bgColor: 'bg-purple-500/20',
        icon: '🟣',
        stars: 3,
        name: 'Épique',
        glow: 'shadow-purple-400/30'
    },
    legendary: {
        color: 'from-amber-400 to-orange-500',
        text: 'text-amber-100',
        border: 'border-amber-400',
        bgColor: 'bg-amber-500/20',
        icon: '🟡',
        stars: 4,
        name: 'Légendaire',
        glow: 'shadow-amber-400/30'
    }
};

const config = rarityConfig[rarity as keyof typeof rarityConfig] || rarityConfig.normal;

const sizeClasses = {
    xs: 'px-2 py-1 text-xs gap-1',
    sm: 'px-2.5 py-1.5 text-xs gap-1.5',
    md: 'px-3 py-2 text-sm gap-2',
    lg: 'px-4 py-2.5 text-base gap-2.5'
};

const starSizeClasses = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg'
};

const getVariantClasses = () => {
    switch (variant) {
        case 'filled':
            return `bg-gradient-to-br ${config.color} ${config.text} shadow-lg ${config.glow}`;
        case 'outlined':
            return `border-2 ${config.border} ${config.text} bg-transparent`;
        case 'minimal':
            return `${config.bgColor} ${config.text} border border-opacity-30 ${config.border}`;
        default:
            return `bg-gradient-to-br ${config.color} ${config.text} shadow-lg ${config.glow}`;
    }
};
</script>

<template>
    <div
        :class="[
            'inline-flex items-center justify-center font-bold rounded-full transition-all duration-200 hover:scale-105 backdrop-blur-sm',
            getVariantClasses(),
            sizeClasses[size]
        ]"
    >
        <!-- Icône de rareté -->
        <span class="opacity-90">{{ config.icon }}</span>

        <!-- Étoiles -->
        <div class="flex items-center">
            <span
                v-for="i in config.stars"
                :key="i"
                :class="['animate-pulse', starSizeClasses[size]]"
                :style="{ animationDelay: `${(i - 1) * 0.2}s` }"
            >
                ⭐
            </span>
        </div>

        <!-- Texte optionnel -->
        <span v-if="showText" class="font-semibold capitalize tracking-wide">
            {{ config.name }}
        </span>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 2s infinite;
}
</style>
