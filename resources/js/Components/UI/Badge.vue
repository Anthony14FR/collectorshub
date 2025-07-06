<script setup lang="ts">
interface Props {
    variant?: 'primary' | 'secondary' | 'accent' | 'neutral' | 'info' | 'success' | 'warning' | 'error' | 'ghost';
    size?: 'xs' | 'sm' | 'md' | 'lg';
    outline?: boolean;
    dot?: boolean;
    pill?: boolean;
    pulse?: boolean;
    removable?: boolean;
    icon?: string;
}

const { 
    variant = 'primary', 
    size = 'md', 
    outline = false,
    dot = false,
    pill = false,
    pulse = false,
    removable = false,
    icon
} = defineProps<Props>();

const emit = defineEmits<{
    remove: [];
}>();

const handleRemove = () => {
    emit('remove');
};

const baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200';

const variantClasses = {
    primary: outline 
        ? 'border-2 border-primary/50 text-primary bg-primary/10 hover:bg-primary/20' 
        : 'bg-gradient-to-br from-primary to-primary/80 text-primary-content shadow-lg shadow-primary/20',
    secondary: outline 
        ? 'border-2 border-secondary/50 text-secondary bg-secondary/10 hover:bg-secondary/20' 
        : 'bg-gradient-to-br from-secondary to-secondary/80 text-secondary-content shadow-lg shadow-secondary/20',
    accent: outline 
        ? 'border-2 border-accent/50 text-accent bg-accent/10 hover:bg-accent/20' 
        : 'bg-gradient-to-br from-accent to-accent/80 text-accent-content shadow-lg shadow-accent/20',
    neutral: outline 
        ? 'border-2 border-neutral/50 text-neutral bg-neutral/10 hover:bg-neutral/20' 
        : 'bg-gradient-to-br from-neutral to-neutral/80 text-neutral-content shadow-lg shadow-neutral/20',
    info: outline 
        ? 'border-2 border-info/50 text-info bg-info/10 hover:bg-info/20' 
        : 'bg-gradient-to-br from-info to-info/80 text-info-content shadow-lg shadow-info/20',
    success: outline 
        ? 'border-2 border-success/50 text-success bg-success/10 hover:bg-success/20' 
        : 'bg-gradient-to-br from-success to-success/80 text-success-content shadow-lg shadow-success/20',
    warning: outline 
        ? 'border-2 border-warning/50 text-warning bg-warning/10 hover:bg-warning/20' 
        : 'bg-gradient-to-br from-warning to-warning/80 text-warning-content shadow-lg shadow-warning/20',
    error: outline 
        ? 'border-2 border-error/50 text-error bg-error/10 hover:bg-error/20' 
        : 'bg-gradient-to-br from-error to-error/80 text-error-content shadow-lg shadow-error/20',
    ghost: 'bg-base-100/50 text-base-content border border-base-content/20 hover:bg-base-100/80'
};

const sizeClasses = {
    xs: 'px-2 py-1 text-xs gap-1',
    sm: 'px-2.5 py-1.5 text-sm gap-1.5',
    md: 'px-3 py-2 text-sm gap-2',
    lg: 'px-4 py-2.5 text-base gap-2.5'
};

const roundingClasses = pill ? 'rounded-full' : 'rounded-lg';
</script>

<template>
    <span 
        :class="[
            baseClasses,
            variantClasses[variant],
            sizeClasses[size],
            roundingClasses,
            { 'animate-pulse': pulse }
        ]"
    >
        <!-- Dot indicator -->
        <span 
            v-if="dot"
            :class="`w-2 h-2 rounded-full bg-current opacity-80 ${size === 'xs' ? 'mr-1' : 'mr-1.5'}`"
        ></span>

        <!-- Icon -->
        <svg 
            v-if="icon"
            :class="`w-${size === 'xs' ? '3' : size === 'sm' ? '4' : '4'} h-${size === 'xs' ? '3' : size === 'sm' ? '4' : '4'} opacity-90`"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path 
                v-if="icon === 'star'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
            />
            <path 
                v-else-if="icon === 'fire'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"
            />
            <path 
                v-else-if="icon === 'shield'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
            />
            <path 
                v-else-if="icon === 'heart'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
            <path 
                v-else-if="icon === 'check'"
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M5 13l4 4L19 7"
            />
        </svg>

        <!-- Content -->
        <slot />

        <!-- Remove button -->
        <button 
            v-if="removable"
            @click="handleRemove"
            :class="`ml-1 p-0.5 rounded-full hover:bg-black/20 transition-colors duration-200 ${size === 'xs' ? 'w-3 h-3' : 'w-4 h-4'}`"
        >
            <svg :class="`w-full h-full opacity-70`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </span>
</template> 