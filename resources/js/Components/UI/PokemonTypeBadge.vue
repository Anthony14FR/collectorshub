<script setup lang="ts">
interface Props {
    type: string;
    size?: 'xs' | 'sm' | 'md' | 'lg';
}

const { type, size = 'sm' } = defineProps<Props>();

const typeConfig: Record<string, { color: string; bg: string; emoji: string }> = {
    // Types de base
    normal: { color: 'text-neutral-content', bg: 'from-neutral to-neutral/80', emoji: '⚪' },
    fire: { color: 'text-error-content', bg: 'from-red-500 to-orange-600', emoji: '🔥' },
    water: { color: 'text-info-content', bg: 'from-blue-500 to-cyan-600', emoji: '💧' },
    electric: { color: 'text-warning-content', bg: 'from-yellow-400 to-yellow-600', emoji: '⚡' },
    grass: { color: 'text-success-content', bg: 'from-green-500 to-emerald-600', emoji: '🌿' },
    ice: { color: 'text-info-content', bg: 'from-cyan-300 to-blue-400', emoji: '❄️' },
    fighting: { color: 'text-error-content', bg: 'from-red-700 to-red-900', emoji: '👊' },
    poison: { color: 'text-purple-100', bg: 'from-purple-500 to-purple-700', emoji: '☠️' },
    ground: { color: 'text-yellow-100', bg: 'from-yellow-600 to-amber-700', emoji: '🏔️' },
    flying: { color: 'text-sky-100', bg: 'from-sky-300 to-indigo-500', emoji: '🪶' },
    psychic: { color: 'text-pink-100', bg: 'from-pink-400 to-purple-600', emoji: '🔮' },
    bug: { color: 'text-green-100', bg: 'from-lime-500 to-green-700', emoji: '🐛' },
    rock: { color: 'text-stone-100', bg: 'from-stone-500 to-gray-700', emoji: '🪨' },
    ghost: { color: 'text-purple-100', bg: 'from-indigo-600 to-purple-800', emoji: '👻' },
    dragon: { color: 'text-indigo-100', bg: 'from-indigo-600 to-purple-700', emoji: '🐉' },
    dark: { color: 'text-gray-100', bg: 'from-gray-700 to-black', emoji: '🌑' },
    steel: { color: 'text-slate-100', bg: 'from-slate-400 to-gray-600', emoji: '⚙️' },
    fairy: { color: 'text-pink-100', bg: 'from-pink-300 to-pink-500', emoji: '🧚' },
};

const config = typeConfig[type.toLowerCase()] || typeConfig.normal;

const sizeClasses = {
    xs: 'px-1.5 py-0.5 text-xs gap-1',
    sm: 'px-2 py-1 text-xs gap-1.5',
    md: 'px-2.5 py-1.5 text-sm gap-2',
    lg: 'px-3 py-2 text-base gap-2.5'
};
</script>

<template>
    <span 
        :class="[
            'inline-flex items-center justify-center font-semibold rounded-full shadow-lg transition-all duration-200 hover:scale-105',
            `bg-gradient-to-br ${config.bg}`,
            config.color,
            sizeClasses[size]
        ]"
    >
        <span class="opacity-80">{{ config.emoji }}</span>
        <span class="capitalize font-bold tracking-wide">{{ type }}</span>
    </span>
</template> 