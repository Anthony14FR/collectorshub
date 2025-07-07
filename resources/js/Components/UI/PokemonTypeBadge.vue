<script setup lang="ts">
interface Props {
    type: string;
    size?: 'xs' | 'sm' | 'md' | 'lg';
    variant?: 'filled' | 'outlined' | 'minimal';
    showIcon?: boolean;
    showText?: boolean;
}

const { type, size = 'sm', variant = 'filled', showIcon = true, showText = true } = defineProps<Props>();

const typeConfig: Record<string, {
    color: string;
    bg: string;
    emoji: string;
    text: string;
    border: string;
    bgColor: string;
    glow: string;
}> = {
    normal: {
        color: 'text-slate-100',
        bg: 'from-slate-400 to-slate-600',
        emoji: '⚪',
        text: 'Normal',
        border: 'border-slate-400',
        bgColor: 'bg-slate-500/20',
        glow: 'shadow-slate-400/20'
    },
    fire: {
        color: 'text-red-100',
        bg: 'from-red-500 to-orange-600',
        emoji: '🔥',
        text: 'Feu',
        border: 'border-red-500',
        bgColor: 'bg-red-500/20',
        glow: 'shadow-red-500/20'
    },
    water: {
        color: 'text-blue-100',
        bg: 'from-blue-500 to-cyan-600',
        emoji: '💧',
        text: 'Eau',
        border: 'border-blue-500',
        bgColor: 'bg-blue-500/20',
        glow: 'shadow-blue-500/20'
    },
    electric: {
        color: 'text-yellow-900',
        bg: 'from-yellow-400 to-yellow-600',
        emoji: '⚡',
        text: 'Électrik',
        border: 'border-yellow-500',
        bgColor: 'bg-yellow-500/20',
        glow: 'shadow-yellow-500/20'
    },
    grass: {
        color: 'text-green-100',
        bg: 'from-green-500 to-emerald-600',
        emoji: '🌿',
        text: 'Plante',
        border: 'border-green-500',
        bgColor: 'bg-green-500/20',
        glow: 'shadow-green-500/20'
    },
    ice: {
        color: 'text-cyan-900',
        bg: 'from-cyan-300 to-blue-400',
        emoji: '❄️',
        text: 'Glace',
        border: 'border-cyan-400',
        bgColor: 'bg-cyan-400/20',
        glow: 'shadow-cyan-400/20'
    },
    fighting: {
        color: 'text-red-100',
        bg: 'from-red-700 to-red-900',
        emoji: '👊',
        text: 'Combat',
        border: 'border-red-700',
        bgColor: 'bg-red-700/20',
        glow: 'shadow-red-700/20'
    },
    poison: {
        color: 'text-purple-100',
        bg: 'from-purple-500 to-purple-700',
        emoji: '☠️',
        text: 'Poison',
        border: 'border-purple-600',
        bgColor: 'bg-purple-600/20',
        glow: 'shadow-purple-600/20'
    },
    ground: {
        color: 'text-yellow-100',
        bg: 'from-yellow-600 to-amber-700',
        emoji: '🏔️',
        text: 'Sol',
        border: 'border-yellow-700',
        bgColor: 'bg-yellow-700/20',
        glow: 'shadow-yellow-700/20'
    },
    flying: {
        color: 'text-sky-100',
        bg: 'from-sky-300 to-indigo-500',
        emoji: '🪶',
        text: 'Vol',
        border: 'border-sky-400',
        bgColor: 'bg-sky-400/20',
        glow: 'shadow-sky-400/20'
    },
    psychic: {
        color: 'text-pink-100',
        bg: 'from-pink-400 to-purple-600',
        emoji: '🔮',
        text: 'Psy',
        border: 'border-pink-500',
        bgColor: 'bg-pink-500/20',
        glow: 'shadow-pink-500/20'
    },
    bug: {
        color: 'text-green-100',
        bg: 'from-lime-500 to-green-700',
        emoji: '🐛',
        text: 'Insecte',
        border: 'border-lime-600',
        bgColor: 'bg-lime-600/20',
        glow: 'shadow-lime-600/20'
    },
    rock: {
        color: 'text-stone-100',
        bg: 'from-stone-500 to-gray-700',
        emoji: '🪨',
        text: 'Roche',
        border: 'border-stone-600',
        bgColor: 'bg-stone-600/20',
        glow: 'shadow-stone-600/20'
    },
    ghost: {
        color: 'text-purple-100',
        bg: 'from-indigo-600 to-purple-800',
        emoji: '👻',
        text: 'Spectre',
        border: 'border-indigo-700',
        bgColor: 'bg-indigo-700/20',
        glow: 'shadow-indigo-700/20'
    },
    dragon: {
        color: 'text-indigo-100',
        bg: 'from-indigo-600 to-purple-700',
        emoji: '🐉',
        text: 'Dragon',
        border: 'border-indigo-600',
        bgColor: 'bg-indigo-600/20',
        glow: 'shadow-indigo-600/20'
    },
    dark: {
        color: 'text-gray-100',
        bg: 'from-gray-700 to-black',
        emoji: '🌑',
        text: 'Ténèbres',
        border: 'border-gray-700',
        bgColor: 'bg-gray-700/20',
        glow: 'shadow-gray-700/20'
    },
    steel: {
        color: 'text-slate-100',
        bg: 'from-slate-400 to-gray-600',
        emoji: '⚙️',
        text: 'Acier',
        border: 'border-slate-500',
        bgColor: 'bg-slate-500/20',
        glow: 'shadow-slate-500/20'
    },
    fairy: {
        color: 'text-pink-100',
        bg: 'from-pink-300 to-pink-500',
        emoji: '🧚',
        text: 'Fée',
        border: 'border-pink-400',
        bgColor: 'bg-pink-400/20',
        glow: 'shadow-pink-400/20'
    },
};

const config = typeConfig[type.toLowerCase()] || typeConfig.normal;

const sizeClasses = {
    xs: 'px-1.5 py-0.5 text-xs gap-1',
    sm: 'px-2 py-1 text-xs gap-1.5',
    md: 'px-2.5 py-1.5 text-sm gap-2',
    lg: 'px-3 py-2 text-base gap-2.5'
};

const iconSizeClasses = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg'
};

const getVariantClasses = () => {
    switch (variant) {
        case 'filled':
            return `bg-gradient-to-br ${config.bg} ${config.color} shadow-lg ${config.glow}`;
        case 'outlined':
            return `border-2 ${config.border} ${config.color} bg-transparent`;
        case 'minimal':
            return `${config.bgColor} ${config.color} border border-opacity-30 ${config.border}`;
        default:
            return `bg-gradient-to-br ${config.bg} ${config.color} shadow-lg ${config.glow}`;
    }
};
</script>

<template>
    <span
        :class="[
            'inline-flex items-center justify-center font-semibold rounded-full transition-all duration-200 hover:scale-105 backdrop-blur-sm',
            getVariantClasses(),
            sizeClasses[size]
        ]"
    >
        <span v-if="showIcon" :class="['opacity-90', iconSizeClasses[size]]">
            {{ config.emoji }}
        </span>

        <span v-if="showText" class="font-bold tracking-wide capitalize">
            {{ config.text }}
        </span>
    </span>
</template>

<style scoped>
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>
