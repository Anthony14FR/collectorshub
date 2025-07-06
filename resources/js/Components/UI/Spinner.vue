<script setup lang="ts">
interface Props {
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    variant?: 'spin' | 'bounce' | 'pulse' | 'dots' | 'bars';
    color?: 'primary' | 'secondary' | 'accent' | 'neutral' | 'base-content';
    speed?: 'slow' | 'normal' | 'fast';
    text?: string;
    overlay?: boolean;
}

const { 
    size = 'md',
    variant = 'spin',
    color = 'primary',
    speed = 'normal',
    text,
    overlay = false
} = defineProps<Props>();

const sizeClasses = {
    xs: 'w-4 h-4',
    sm: 'w-6 h-6',
    md: 'w-8 h-8',
    lg: 'w-12 h-12',
    xl: 'w-16 h-16'
};

const colorClasses = {
    primary: 'text-primary',
    secondary: 'text-secondary',
    accent: 'text-accent',
    neutral: 'text-neutral',
    'base-content': 'text-base-content'
};

const speedClasses = {
    slow: 'animate-spin-slow',
    normal: 'animate-spin',
    fast: 'animate-spin-fast'
};

const dotSizeClasses = {
    xs: 'w-1 h-1',
    sm: 'w-1.5 h-1.5',
    md: 'w-2 h-2',
    lg: 'w-3 h-3',
    xl: 'w-4 h-4'
};

const barSizeClasses = {
    xs: 'w-0.5 h-3',
    sm: 'w-1 h-4',
    md: 'w-1 h-6',
    lg: 'w-1.5 h-8',
    xl: 'w-2 h-10'
};
</script>

<template>
    <!-- Overlay backdrop -->
    <div 
        v-if="overlay"
        class="fixed inset-0 bg-base-300/50 backdrop-blur-sm flex items-center justify-center z-50"
    >
        <div class="bg-base-100 rounded-xl p-6 shadow-xl border border-base-300/30 flex flex-col items-center gap-4">
            <!-- Spinner content will be rendered here -->
            <div class="flex items-center justify-center" :class="sizeClasses[size]">
                <!-- Spin variant -->
                <svg 
                    v-if="variant === 'spin'"
                    :class="[colorClasses[color], speedClasses[speed]]"
                    fill="none" 
                    viewBox="0 0 24 24"
                >
                    <circle 
                        class="opacity-25" 
                        cx="12" 
                        cy="12" 
                        r="10" 
                        stroke="currentColor" 
                        stroke-width="4"
                    />
                    <path 
                        class="opacity-75" 
                        fill="currentColor" 
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    />
                </svg>

                <!-- Bounce variant -->
                <div 
                    v-else-if="variant === 'bounce'"
                    :class="[sizeClasses[size], colorClasses[color]]"
                    class="rounded-full bg-current animate-bounce"
                />

                <!-- Pulse variant -->
                <div 
                    v-else-if="variant === 'pulse'"
                    :class="[sizeClasses[size], colorClasses[color]]"
                    class="rounded-full bg-current animate-pulse"
                />

                <!-- Dots variant -->
                <div 
                    v-else-if="variant === 'dots'"
                    class="flex space-x-2"
                >
                    <div 
                        v-for="i in 3" 
                        :key="i"
                        :class="[dotSizeClasses[size], colorClasses[color]]"
                        class="rounded-full bg-current animate-bounce"
                        :style="{ animationDelay: `${(i - 1) * 0.1}s` }"
                    />
                </div>

                <!-- Bars variant -->
                <div 
                    v-else-if="variant === 'bars'"
                    class="flex space-x-1 items-end"
                >
                    <div 
                        v-for="i in 4" 
                        :key="i"
                        :class="[barSizeClasses[size], colorClasses[color]]"
                        class="rounded-sm bg-current animate-pulse"
                        :style="{ animationDelay: `${(i - 1) * 0.15}s` }"
                    />
                </div>
            </div>

            <p v-if="text" class="text-sm text-base-content/70 text-center">{{ text }}</p>
        </div>
    </div>

    <!-- Inline spinner -->
    <div v-else class="flex items-center justify-center gap-3">
        <div class="flex items-center justify-center" :class="sizeClasses[size]">
            <!-- Spin variant -->
            <svg 
                v-if="variant === 'spin'"
                :class="[colorClasses[color], speedClasses[speed]]"
                fill="none" 
                viewBox="0 0 24 24"
            >
                <circle 
                    class="opacity-25" 
                    cx="12" 
                    cy="12" 
                    r="10" 
                    stroke="currentColor" 
                    stroke-width="4"
                />
                <path 
                    class="opacity-75" 
                    fill="currentColor" 
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
            </svg>

            <!-- Bounce variant -->
            <div 
                v-else-if="variant === 'bounce'"
                :class="[sizeClasses[size], colorClasses[color]]"
                class="rounded-full bg-current animate-bounce"
            />

            <!-- Pulse variant -->
            <div 
                v-else-if="variant === 'pulse'"
                :class="[sizeClasses[size], colorClasses[color]]"
                class="rounded-full bg-current animate-pulse"
            />

            <!-- Dots variant -->
            <div 
                v-else-if="variant === 'dots'"
                class="flex space-x-2"
            >
                <div 
                    v-for="i in 3" 
                    :key="i"
                    :class="[dotSizeClasses[size], colorClasses[color]]"
                    class="rounded-full bg-current animate-bounce"
                    :style="{ animationDelay: `${(i - 1) * 0.1}s` }"
                />
            </div>

            <!-- Bars variant -->
            <div 
                v-else-if="variant === 'bars'"
                class="flex space-x-1 items-end"
            >
                <div 
                    v-for="i in 4" 
                    :key="i"
                    :class="[barSizeClasses[size], colorClasses[color]]"
                    class="rounded-sm bg-current animate-pulse"
                    :style="{ animationDelay: `${(i - 1) * 0.15}s` }"
                />
            </div>
        </div>

        <span v-if="text" class="text-sm text-base-content/70">{{ text }}</span>
    </div>
</template>

<style scoped>
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes spin-fast {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spin-slow 2s linear infinite;
}

.animate-spin-fast {
    animation: spin-fast 0.5s linear infinite;
}
</style> 