<script setup lang="ts">
interface Props {
  variant?: 'primary' | 'secondary' | 'ghost' | 'outline' | 'invocation' | 'marketplace' | 'leaderboard' | 'success';
  size?: 'sm' | 'md' | 'lg';
  icon?: string;
  disabled?: boolean;
  type?: 'button' | 'submit' | 'reset';
}

const {
  variant = 'primary',
  size = 'md',
  icon,
  disabled = false,
  type = 'button'
} = defineProps<Props>();

const emit = defineEmits<{
  click: [event: MouseEvent];
}>();

const handleClick = (event: MouseEvent) => {
  if (!disabled) {
    emit('click', event);
  }
};

const baseClasses = 'group relative overflow-hidden font-medium transition-all duration-300 cursor-pointer focus:outline-none focus:ring-0 before:absolute before:inset-0 before:bg-gradient-to-r before:-translate-x-full hover:before:translate-x-full before:transition-transform before:duration-700';

const variantClasses = {
  primary: 'text-base-100 bg-gradient-to-br from-primary to-secondary border border-primary/30 hover:border-primary/50 shadow-lg shadow-primary/15 hover:shadow-xl hover:shadow-primary/20 hover:-translate-y-0.5 before:from-white/0 before:via-white/12 before:to-white/0 font-semibold',
  secondary: 'text-base-content/90 hover:text-secondary bg-gradient-to-br from-base-100/60 via-base-200/70 to-base-100/60 backdrop-blur-sm border border-secondary/20 hover:border-secondary/40 shadow-lg shadow-secondary/15 hover:shadow-xl hover:shadow-secondary/20 hover:-translate-y-0.5 before:from-secondary/0 before:via-secondary/8 before:to-secondary/0 font-semibold',
  ghost: 'text-base-content/80 hover:text-primary hover:bg-gradient-to-r hover:from-primary/5 hover:to-secondary/5 before:from-primary/0 before:via-primary/10 before:to-primary/0 font-medium',
  outline: 'text-primary border-2 border-primary/30 hover:border-primary/50 hover:bg-primary/5 before:from-primary/0 before:via-primary/10 before:to-primary/0 font-semibold',
  invocation: 'text-accent !border-accent/30 !bg-gradient-to-r !from-accent/10 !to-accent/20 hover:!from-accent/20 hover:!to-accent/30 before:from-accent/0 before:via-accent/10 before:to-accent/0 font-semibold',
  marketplace: 'h-12 w-12 flex-shrink-0 !rounded-full !border-warning/30 !bg-gradient-to-r !from-warning/10 !to-warning/20 !p-0 !text-warning hover:!from-warning/20 hover:!to-warning/30 before:from-warning/0 before:via-warning/10 before:to-warning/0 font-semibold',
  leaderboard: 'w-full !border-primary/30 !bg-gradient-to-r !from-primary/10 !to-primary/20 !text-primary hover:!from-primary/20 hover:!to-primary/30 before:from-primary/0 before:via-primary/10 before:to-primary/0 font-semibold',
  success: 'text-success !border-success/30 !bg-gradient-to-r !from-success/10 !to-success/20 hover:!from-success/20 hover:!to-success/30 before:from-success/0 before:via-success/10 before:to-success/0 font-semibold'
};

const sizeClasses = {
  sm: 'px-4 py-2 text-sm',
  md: 'px-6 py-3 text-sm',
  lg: 'px-8 py-4 text-base'
};

const disabledClasses = 'opacity-50 cursor-not-allowed hover:transform-none hover:shadow-none';

const roundingClasses = {
  sm: 'before:rounded-lg',
  md: 'before:rounded-xl',
  lg: 'before:rounded-2xl'
};
</script>

<template>
  <button
    :type="type"
    :disabled="disabled"
    :class="[
      baseClasses,
      variantClasses[variant],
      sizeClasses[size],
      roundingClasses[size],
      disabled ? disabledClasses : ''
    ]"
    @click="handleClick"
  >
    <span class="relative z-10 flex items-center justify-center gap-2">
      <div
        v-if="icon"
        class="w-4 h-4 rounded-md bg-white/20 flex items-center justify-center group-hover:bg-white/30 transition-colors duration-300"
        :class="{ 'group-hover:bg-primary/25': variant === 'secondary' }"
      >
        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            v-if="icon === 'lightning'"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 10V3L4 14h7v7l9-11h-7z"
          />
          <path
            v-else-if="icon === 'collection'"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
          />
          <path
            v-else-if="icon === 'user'"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
          />
          <path
            v-else-if="icon === 'gacha'"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"
          />
        </svg>
      </div>
      <slot />
    </span>
  </button>
</template>
