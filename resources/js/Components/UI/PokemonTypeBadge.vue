<script setup lang="ts">
interface Props {
    type: string;
    size?: 'xs' | 'sm' | 'md' | 'lg';
}

const { type, size = 'sm' } = defineProps<Props>();

const typeConfig: Record<string, { color: string; bg: string; fileName: string }> = {
    normal: { color: 'text-neutral-content', bg: 'from-neutral to-neutral/80', fileName: 'Normal' },
    fire: { color: 'text-error-content', bg: 'from-red-500 to-orange-600', fileName: 'Feu' },
    water: { color: 'text-info-content', bg: 'from-blue-500 to-cyan-600', fileName: 'Eau' },
    electric: { color: 'text-warning-content', bg: 'from-yellow-400 to-yellow-600', fileName: 'Électrik' },
    grass: { color: 'text-success-content', bg: 'from-green-500 to-emerald-600', fileName: 'Plante' },
    ice: { color: 'text-info-content', bg: 'from-cyan-300 to-blue-400', fileName: 'Glace' },
    fighting: { color: 'text-error-content', bg: 'from-red-700 to-red-900', fileName: 'Combat' },
    poison: { color: 'text-purple-100', bg: 'from-purple-500 to-purple-700', fileName: 'Poison' },
    ground: { color: 'text-yellow-100', bg: 'from-yellow-600 to-amber-700', fileName: 'Sol' },
    flying: { color: 'text-sky-100', bg: 'from-sky-300 to-indigo-500', fileName: 'Vol' },
    psychic: { color: 'text-pink-100', bg: 'from-pink-400 to-purple-600', fileName: 'Psy' },
    bug: { color: 'text-green-100', bg: 'from-lime-500 to-green-700', fileName: 'Insecte' },
    rock: { color: 'text-stone-100', bg: 'from-stone-500 to-gray-700', fileName: 'Roche' },
    ghost: { color: 'text-purple-100', bg: 'from-indigo-600 to-purple-800', fileName: 'Spectre' },
    dragon: { color: 'text-indigo-100', bg: 'from-indigo-600 to-purple-700', fileName: 'Dragon' },
    dark: { color: 'text-gray-100', bg: 'from-gray-700 to-black', fileName: 'Ténèbres' },
    steel: { color: 'text-slate-100', bg: 'from-slate-400 to-gray-600', fileName: 'Acier' },
    fairy: { color: 'text-pink-100', bg: 'from-pink-300 to-pink-500', fileName: 'Fée' },
};

const config = typeConfig[type.toLowerCase()] || typeConfig.normal;

const sizeClasses = {
    xs: 'px-1.5 py-0.5 text-xs gap-1',
    sm: 'px-2 py-1 text-xs gap-1.5',
    md: 'px-2.5 py-1.5 text-sm gap-2',
    lg: 'px-3 py-2 text-base gap-2.5'
};

const iconSizeClasses = {
    xs: 'w-3 h-3',
    sm: 'w-4 h-4',
    md: 'w-5 h-5',
    lg: 'w-6 h-6'
};

const formatType = (type: string) => {
    const typeLabels: Record<string, string> = {
        normal: 'Normal',
        fire: 'Feu',
        water: 'Eau',
        electric: 'Électrik',
        grass: 'Plante',
        ice: 'Glace',
        fighting: 'Combat',
        poison: 'Poison',
        ground: 'Sol',
        flying: 'Vol',
        psychic: 'Psy',
        bug: 'Insecte',
        rock: 'Roche',
        ghost: 'Spectre',
        dragon: 'Dragon',
        dark: 'Ténèbres',
        steel: 'Acier',
        fairy: 'Fée'
    };
    return typeLabels[type.toLowerCase()] || type;
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
        <img
            :src="`/images/types/${config.fileName}.png`"
            :alt="formatType(type)"
            :class="[iconSizeClasses[size], 'object-contain']"
            style="image-rendering: pixelated;"
        />
        <span class="capitalize font-bold tracking-wide">{{ formatType(type) }}</span>
    </span>
</template>