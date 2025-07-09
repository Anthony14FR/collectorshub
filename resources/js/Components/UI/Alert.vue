<script setup lang="ts">
interface Props {
    type?: 'info' | 'success' | 'warning' | 'error';
    variant?: 'filled' | 'outlined' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    icon?: string;
    title?: string;
    message?: string;
    dismissible?: boolean;
    show?: boolean;
}

const {
    type = 'info',
    variant = 'ghost',
    size = 'md',
    icon,
    title,
    message,
    dismissible = false,
    show = true
} = defineProps<Props>();

const emit = defineEmits<{
    dismiss: [];
}>();

const handleDismiss = () => {
    emit('dismiss');
};

const typeConfig = {
    info: {
        color: 'info',
        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        emoji: 'ℹ️'
    },
    success: {
        color: 'success',
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        emoji: '✅'
    },
    warning: {
        color: 'warning',
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
        emoji: '⚠️'
    },
    error: {
        color: 'error',
        icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        emoji: '❌'
    }
};

const variantClasses = {
    filled: `bg-gradient-to-br from-${typeConfig[type].color} to-${typeConfig[type].color}/80 text-${typeConfig[type].color}-content border-${typeConfig[type].color}/30`,
    outlined: `bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg border-2 border-${typeConfig[type].color}/40 text-${typeConfig[type].color}`,
    ghost: `bg-gradient-to-br from-${typeConfig[type].color}/5 to-${typeConfig[type].color}/3 border border-${typeConfig[type].color}/30 text-${typeConfig[type].color}`
};

const sizeClasses = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-3 text-base',
    lg: 'px-6 py-4 text-lg'
};
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
    >
        <div
            v-if="show"
            :class="[
                'relative rounded-xl shadow-lg transition-all duration-300 overflow-hidden',
                variantClasses[variant],
                sizeClasses[size]
            ]"
            role="alert"
        >
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div :class="`absolute top-2 left-4 w-1 h-1 bg-${typeConfig[type].color}/40 rounded-full animate-pulse opacity-60`"></div>
                <div :class="`absolute top-4 right-6 w-1.5 h-1.5 bg-${typeConfig[type].color}/40 rounded-full animate-pulse delay-300 opacity-40`"></div>
                <div :class="`absolute bottom-2 left-8 w-1 h-1 bg-${typeConfig[type].color}/40 rounded-full animate-pulse delay-700 opacity-50`"></div>
            </div>

            <div class="relative z-10 flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <div v-if="icon || !$slots.icon"
                         :class="`w-6 h-6 rounded-lg bg-${typeConfig[type].color}/20 flex items-center justify-center`">
                        <span v-if="!icon" class="text-sm">{{ typeConfig[type].emoji }}</span>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon"/>
                        </svg>
                    </div>
                    <slot v-else name="icon" />
                </div>

                <div class="flex-1 min-w-0">
                    <h4 v-if="title" class="font-bold mb-1">{{ title }}</h4>
                    <div :class="title ? 'text-sm opacity-90' : ''">
                        {{ message }}
                        <slot />
                    </div>
                </div>

                <button
                    v-if="dismissible"
                    @click="handleDismiss"
                    class="flex-shrink-0 ml-2 p-1 rounded-lg hover:bg-black/10 transition-colors duration-200"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>
