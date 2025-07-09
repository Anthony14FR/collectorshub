<script setup lang="ts">
interface Props {
    type: string;
    size?: 'xs' | 'sm' | 'md' | 'lg';
}

const { type, size = 'sm' } = defineProps<Props>();

const frenchToEnglish: Record<string, string> = {
    'normal': 'normal',
    'feu': 'fire',
    'eau': 'water',
    'électrik': 'electric',
    'plante': 'grass',
    'glace': 'ice',
    'combat': 'fighting',
    'poison': 'poison',
    'sol': 'ground',
    'vol': 'flying',
    'psy': 'psychic',
    'insecte': 'bug',
    'roche': 'rock',
    'spectre': 'ghost',
    'dragon': 'dragon',
    'ténèbres': 'dark',
    'acier': 'steel',
    'fée': 'fairy'
};

const typeConfig: Record<string, { color: string; bg: string; fileName: string; borderColor: string }> = {
    normal: { color: '#6B7280', bg: 'rgba(107, 114, 128, 0.2)', borderColor: 'rgba(107, 114, 128, 0.3)', fileName: 'Normal' },
    fire: { color: '#DC2626', bg: 'rgba(220, 38, 38, 0.2)', borderColor: 'rgba(220, 38, 38, 0.3)', fileName: 'Feu' },
    water: { color: '#2563EB', bg: 'rgba(37, 99, 235, 0.2)', borderColor: 'rgba(37, 99, 235, 0.3)', fileName: 'Eau' },
    electric: { color: '#D97706', bg: 'rgba(217, 119, 6, 0.2)', borderColor: 'rgba(217, 119, 6, 0.3)', fileName: 'Électrik' },
    grass: { color: '#16A34A', bg: 'rgba(22, 163, 74, 0.2)', borderColor: 'rgba(22, 163, 74, 0.3)', fileName: 'Plante' },
    ice: { color: '#0891B2', bg: 'rgba(8, 145, 178, 0.2)', borderColor: 'rgba(8, 145, 178, 0.3)', fileName: 'Glace' },
    fighting: { color: '#B91C1C', bg: 'rgba(185, 28, 28, 0.2)', borderColor: 'rgba(185, 28, 28, 0.3)', fileName: 'Combat' },
    poison: { color: '#7C3AED', bg: 'rgba(124, 58, 237, 0.2)', borderColor: 'rgba(124, 58, 237, 0.3)', fileName: 'Poison' },
    ground: { color: '#D97706', bg: 'rgba(217, 119, 6, 0.2)', borderColor: 'rgba(217, 119, 6, 0.3)', fileName: 'Sol' },
    flying: { color: '#0284C7', bg: 'rgba(2, 132, 199, 0.2)', borderColor: 'rgba(2, 132, 199, 0.3)', fileName: 'Vol' },
    psychic: { color: '#DB2777', bg: 'rgba(219, 39, 119, 0.2)', borderColor: 'rgba(219, 39, 119, 0.3)', fileName: 'Psy' },
    bug: { color: '#65A30D', bg: 'rgba(101, 163, 13, 0.2)', borderColor: 'rgba(101, 163, 13, 0.3)', fileName: 'Insecte' },
    rock: { color: '#78716C', bg: 'rgba(120, 113, 108, 0.2)', borderColor: 'rgba(120, 113, 108, 0.3)', fileName: 'Roche' },
    ghost: { color: '#6366F1', bg: 'rgba(99, 102, 241, 0.2)', borderColor: 'rgba(99, 102, 241, 0.3)', fileName: 'Spectre' },
    dragon: { color: '#7C3AED', bg: 'rgba(124, 58, 237, 0.2)', borderColor: 'rgba(124, 58, 237, 0.3)', fileName: 'Dragon' },
    dark: { color: '#374151', bg: 'rgba(55, 65, 81, 0.2)', borderColor: 'rgba(55, 65, 81, 0.3)', fileName: 'Ténèbres' },
    steel: { color: '#64748B', bg: 'rgba(100, 116, 139, 0.2)', borderColor: 'rgba(100, 116, 139, 0.3)', fileName: 'Acier' },
    fairy: { color: '#EC4899', bg: 'rgba(236, 72, 153, 0.2)', borderColor: 'rgba(236, 72, 153, 0.3)', fileName: 'Fée' },
};

console.log('Type reçu:', type);

const normalizedType = type.toLowerCase();
const englishType = frenchToEnglish[normalizedType] || normalizedType;

console.log('Type normalisé:', normalizedType);
console.log('Type anglais:', englishType);

const config = typeConfig[englishType] || typeConfig.normal;

console.log('Config trouvée:', config);

const sizeClasses = {
    xs: 'px-2 py-1 text-xs gap-1.5',
    sm: 'px-2.5 py-1.5 text-xs gap-2',
    md: 'px-3 py-2 text-sm gap-2',
    lg: 'px-4 py-2.5 text-base gap-2.5'
};

const iconSizeClasses = {
    xs: 'w-4 h-4',
    sm: 'w-5 h-5',
    md: 'w-6 h-6',
    lg: 'w-7 h-7'
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
    return typeLabels[englishType] || type;
};
</script>

<template>
    <span
        :class="[
            'inline-flex items-center justify-center font-medium rounded-full border transition-all duration-200',
            sizeClasses[size]
        ]"
        :style="{
            backgroundColor: config.bg,
            color: config.color,
            borderColor: config.borderColor
        }"
    >
        <img
            :src="`/images/types/${config.fileName}.png`"
            :alt="formatType(type)"
            :class="[iconSizeClasses[size], 'object-contain']"
            style="image-rendering: pixelated;"
        />
        <span class="font-semibold">{{ formatType(type) }}</span>
    </span>
</template>
