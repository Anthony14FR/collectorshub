<script setup lang="ts">
interface Props {
    variant?: 'default' | 'elevated' | 'outlined' | 'ghost' | 'glass';
    size?: 'sm' | 'md' | 'lg' | 'xl';
    hover?: boolean;
    clickable?: boolean;
    padding?: 'none' | 'sm' | 'md' | 'lg';
    rounded?: 'sm' | 'md' | 'lg' | 'xl' | 'full';
}

const { 
    variant = 'default', 
    size = 'md', 
    hover = false,
    clickable = false,
    padding = 'md',
    rounded = 'lg'
} = defineProps<Props>();

const emit = defineEmits<{
    click: [event: MouseEvent];
}>();

const handleClick = (event: MouseEvent) => {
    if (clickable) {
        emit('click', event);
    }
};

const baseClasses = 'relative overflow-hidden transition-all duration-300';

const variantClasses = {
    default: 'bg-base-100 border border-base-300/30 shadow-sm',
    elevated: 'bg-base-100 shadow-lg shadow-base-300/20 border border-base-300/20',
    outlined: 'bg-transparent border-2 border-base-300/50',
    ghost: 'bg-base-100/50 backdrop-blur-sm',
    glass: 'bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg border border-base-300/30 shadow-xl shadow-base-300/10'
};

const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl'
};

const paddingClasses = {
    none: 'p-0',
    sm: 'p-3',
    md: 'p-4',
    lg: 'p-6'
};

const roundingClasses = {
    sm: 'rounded-md',
    md: 'rounded-lg',
    lg: 'rounded-xl',
    xl: 'rounded-2xl',
    full: 'rounded-3xl'
};

const hoverClasses = hover ? 'hover:shadow-xl hover:shadow-base-300/25 hover:-translate-y-1' : '';
const clickableClasses = clickable ? 'cursor-pointer hover:scale-[1.02] active:scale-[0.98]' : '';
</script>

<template>
    <div 
        :class="[
            baseClasses,
            variantClasses[variant],
            sizeClasses[size],
            paddingClasses[padding],
            roundingClasses[rounded],
            hoverClasses,
            clickableClasses
        ]"
        @click="handleClick"
    >
        <!-- Particules décoratives pour variant glass -->
        <div v-if="variant === 'glass'" class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-4 left-6 w-1 h-1 bg-primary rounded-full animate-pulse opacity-60"></div>
            <div class="absolute top-8 right-8 w-1.5 h-1.5 bg-secondary rounded-full animate-pulse delay-300 opacity-40"></div>
            <div class="absolute bottom-6 left-10 w-1 h-1 bg-accent rounded-full animate-pulse delay-700 opacity-50"></div>
        </div>

        <!-- Header slot -->
        <div v-if="$slots.header" class="mb-4">
            <slot name="header" />
        </div>

        <!-- Main content -->
        <div class="relative z-10">
            <slot />
        </div>

        <!-- Footer slot -->
        <div v-if="$slots.footer" class="mt-4 pt-4 border-t border-base-300/30">
            <slot name="footer" />
        </div>

        <!-- Actions slot -->
        <div v-if="$slots.actions" class="mt-4 flex flex-wrap gap-2">
            <slot name="actions" />
        </div>
    </div>
</template> 