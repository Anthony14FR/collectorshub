<script setup lang="ts">
interface Props {
    src?: string;
    alt?: string;
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl';
    variant?: 'circle' | 'rounded' | 'square';
    status?: 'online' | 'offline' | 'away' | 'busy';
    ring?: boolean;
    ringColor?: 'primary' | 'secondary' | 'accent' | 'success' | 'warning' | 'error';
    placeholder?: string;
    gradient?: boolean;
    fallback?: string;
}

const { 
    src,
    alt = 'Avatar',
    size = 'md',
    variant = 'circle',
    status,
    ring = false,
    ringColor = 'primary',
    placeholder,
    gradient = false,
    fallback
} = defineProps<Props>();

const sizeClasses = {
    xs: 'w-6 h-6',
    sm: 'w-8 h-8',
    md: 'w-12 h-12',
    lg: 'w-16 h-16',
    xl: 'w-20 h-20',
    '2xl': 'w-24 h-24'
};

const variantClasses = {
    circle: 'rounded-full',
    rounded: 'rounded-lg',
    square: 'rounded-md'
};

const ringClasses = ring ? `ring-4 ring-${ringColor}/50 ring-offset-2 ring-offset-base-100` : '';

const statusConfig = {
    online: { color: 'bg-success', label: 'En ligne' },
    offline: { color: 'bg-base-300', label: 'Hors ligne' },
    away: { color: 'bg-warning', label: 'Absent' },
    busy: { color: 'bg-error', label: 'Occupé' }
};

const statusSizeClasses = {
    xs: 'w-2 h-2',
    sm: 'w-2.5 h-2.5',
    md: 'w-3 h-3',
    lg: 'w-4 h-4',
    xl: 'w-5 h-5',
    '2xl': 'w-6 h-6'
};

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map(word => word.charAt(0))
        .slice(0, 2)
        .join('')
        .toUpperCase();
};

const placeholderInitials = placeholder ? getInitials(placeholder) : fallback || '?';
</script>

<template>
    <div class="relative inline-block">
        <!-- Avatar container -->
        <div 
            :class="[
                'relative overflow-hidden transition-all duration-200',
                sizeClasses[size],
                variantClasses[variant],
                ringClasses,
                gradient ? 'bg-gradient-to-br from-primary/20 to-secondary/20' : 'bg-base-200'
            ]"
        >
            <!-- Image -->
            <img 
                v-if="src"
                :src="src"
                :alt="alt"
                class="w-full h-full object-cover"
                loading="lazy"
            />
            
            <!-- Placeholder/Initials -->
            <div 
                v-else
                class="w-full h-full flex items-center justify-center font-semibold text-base-content/70"
                :class="{
                    'text-xs': size === 'xs',
                    'text-sm': size === 'sm',
                    'text-base': size === 'md',
                    'text-lg': size === 'lg',
                    'text-xl': size === 'xl',
                    'text-2xl': size === '2xl'
                }"
            >
                {{ placeholderInitials }}
            </div>

            <!-- Gradient overlay for glass effect -->
            <div 
                v-if="gradient"
                class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent pointer-events-none"
            ></div>
        </div>

        <!-- Status indicator -->
        <div 
            v-if="status"
            :class="[
                'absolute bottom-0 right-0 rounded-full border-2 border-base-100',
                statusSizeClasses[size],
                statusConfig[status].color
            ]"
            :title="statusConfig[status].label"
        ></div>

        <!-- Notification badge -->
        <slot name="badge" />
    </div>
</template> 