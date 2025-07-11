<script setup lang="ts">
interface Option {
  value: string | number;
  label: string;
  disabled?: boolean;
}

interface Props {
  options: Option[];
  size?: 'xs' | 'sm' | 'md' | 'lg';
  variant?: 'default' | 'outlined' | 'ghost' | 'bordered';
  state?: 'default' | 'success' | 'warning' | 'error';
  placeholder?: string;
  label?: string;
  helperText?: string;
  disabled?: boolean;
  required?: boolean;
  modelValue?: string | number;
  multiple?: boolean;
}

const {
  options = [],
  size = 'md',
  variant = 'default',
  state = 'default',
  placeholder,
  label,
  helperText,
  disabled = false,
  required = false,
  modelValue,
  multiple = false
} = defineProps<Props>();

const emit = defineEmits<{
  'update:modelValue': [value: string | number | string[] | number[]];
  focus: [event: FocusEvent];
  blur: [event: FocusEvent];
  change: [event: Event];
}>();

const handleChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  if (multiple) {
    const values = Array.from(target.selectedOptions).map(option => option.value);
    emit('update:modelValue', values);
  } else {
    emit('update:modelValue', target.value);
  }
  emit('change', event);
};

const baseClasses = 'w-full transition-all duration-200 focus:outline-none appearance-none bg-no-repeat bg-right cursor-pointer text-base-content';

const sizeClasses = {
  xs: 'px-2 py-1 text-xs pr-6',
  sm: 'px-3 py-2 text-sm pr-8',
  md: 'px-3 py-2 text-sm pr-8',
  lg: 'px-4 py-3 text-base pr-10'
};

const getVariantClasses = (variant: string) => {
  switch (variant) {
  case 'default':
    return 'bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm';
  case 'outline':
    return 'bg-transparent border border-base-300 focus:border-primary';
  case 'ghost':
    return 'bg-transparent border-none focus:bg-base-200/30';
  case 'bordered':
    return 'bg-base-200/30 border-2 border-base-300 focus:border-primary';
  default:
    return 'bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm';
  }
};

const stateClasses = {
  default: '',
  success: 'border-success/50 focus:border-success',
  warning: 'border-warning/50 focus:border-warning',
  error: 'border-error/50 focus:border-error'
};

const disabledClasses = disabled ? 'opacity-50 cursor-not-allowed' : '';
const roundingClasses = 'rounded-lg';
</script>

<template>
  <div class="space-y-2">
    <label v-if="label" class="block text-xs font-bold tracking-wider text-base-content/80 uppercase">
      {{ label }}
      <span v-if="required" class="text-error ml-1">*</span>
    </label>

    <div class="relative">
      <select
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :multiple="multiple"
        :class="[
          baseClasses,
          sizeClasses[size],
          getVariantClasses(variant),
          stateClasses[state],
          roundingClasses,
          disabledClasses
        ]"
        @change="handleChange"
        @focus="$emit('focus', $event)"
        @blur="$emit('blur', $event)"
      >
        <option v-if="placeholder && !multiple" value="" disabled class="text-base-content/50">{{ placeholder }}</option>
        <option
          v-for="option in options"
          :key="option.value"
          :value="option.value"
          :disabled="option.disabled"
          class="bg-base-200 text-base-content"
        >
          {{ option.label }}
        </option>
      </select>

      <div
        class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none z-10"
        :class="{ 'right-8': state !== 'default' }"
      >
        <svg class="w-4 h-4 text-base-content/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </div>

      <div
        v-if="state !== 'default'"
        class="absolute right-2 top-1/2 -translate-y-1/2 z-10"
      >
        <div
          :class="`w-4 h-4 rounded-full flex items-center justify-center ${
            state === 'success' ? 'bg-success/20 text-success' :
            state === 'warning' ? 'bg-warning/20 text-warning' :
            'bg-error/20 text-error'
          }`"
        >
          <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              v-if="state === 'success'"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            />
            <path
              v-else-if="state === 'warning'"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
            />
            <path
              v-else
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>
      </div>
    </div>

    <p
      v-if="helperText"
      :class="`text-xs ${
        state === 'success' ? 'text-success' :
        state === 'warning' ? 'text-warning' :
        state === 'error' ? 'text-error' :
        'text-base-content/60'
      }`"
    >
      {{ helperText }}
    </p>
  </div>
</template> 