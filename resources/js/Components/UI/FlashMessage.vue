<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  show: boolean;
  type: 'success' | 'error' | 'info';
  title: string;
  message: string;
}>();

const iconClasses = computed(() => {
  switch (props.type) {
  case 'success': return 'text-success';
  case 'error': return 'text-error';
  default: return 'text-info';
  }
});

const borderClasses = computed(() => {
  switch (props.type) {
  case 'success': return 'from-success/25 to-success/15 border-success/40';
  case 'error': return 'from-error/25 to-error/15 border-error/40';
  default: return 'from-info/25 to-info/15 border-info/40';
  }
});
</script>

<template>
  <Transition enter-active-class="transition-all duration-500 ease-out"
              enter-from-class="transform translate-x-full opacity-0" enter-to-class="transform translate-x-0 opacity-100"
              leave-active-class="transition-all duration-300 ease-in" leave-from-class="transform translate-x-0 opacity-100"
              leave-to-class="transform translate-x-full opacity-0">
    <div v-if="show" class="fixed bottom-6 right-6 z-50 max-w-sm">
      <div class="bg-gradient-to-br from-base-100/95 to-base-200/90 backdrop-blur-lg border-2 rounded-xl p-4 shadow-2xl"
           :class="borderClasses">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-base-content/10 rounded-full flex items-center justify-center flex-shrink-0"
               :class="iconClasses">
            <svg v-if="type === 'success'" class="w-4 h-4" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else-if="type === 'error'" class="w-4 h-4" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm" :class="iconClasses">
              {{ title }}
            </p>
            <p class="text-xs text-base-content/80 break-words">
              {{ message }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>