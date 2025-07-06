<script setup lang="ts">
interface Props {
    type?: 'text' | 'email' | 'password' | 'number' | 'search' | 'tel' | 'url';
    size?: 'sm' | 'md' | 'lg';
    variant?: 'default' | 'outlined' | 'ghost' | 'bordered';
    state?: 'default' | 'success' | 'warning' | 'error';
    placeholder?: string;
    label?: string;
    helperText?: string;
    disabled?: boolean;
    readonly?: boolean;
    required?: boolean;
    modelValue?: string | number;
    prefix?: string;
    suffix?: string;
    icon?: string;
    clearable?: boolean;
}

const { 
    type = 'text',
    size = 'md',
    variant = 'default',
    state = 'default',
    placeholder,
    label,
    helperText,
    disabled = false,
    readonly = false,
    required = false,
    modelValue,
    prefix,
    suffix,
    icon,
    clearable = false
} = defineProps<Props>();

const emit = defineEmits<{
    'update:modelValue': [value: string | number];
    focus: [event: FocusEvent];
    blur: [event: FocusEvent];
    clear: [];
}>();

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', type === 'number' ? Number(target.value) : target.value);
};

const handleClear = () => {
    emit('update:modelValue', '');
    emit('clear');
};

const baseClasses = 'w-full transition-all duration-200 focus:outline-none placeholder:text-base-content/50';

const sizeClasses = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-3 text-base',
    lg: 'px-5 py-4 text-lg'
};

const variantClasses = {
    default: 'bg-base-100 border border-base-300/50 focus:border-primary/50 focus:ring-2 focus:ring-primary/20',
    outlined: 'bg-transparent border-2 border-base-300/50 focus:border-primary/50',
    ghost: 'bg-base-100/50 backdrop-blur-sm border border-base-300/30 focus:border-primary/50',
    bordered: 'bg-gradient-to-br from-base-100 to-base-200/50 border-2 border-base-300/50 focus:border-primary/50 shadow-sm'
};

const stateClasses = {
    default: '',
    success: 'border-success/50 focus:border-success focus:ring-success/20',
    warning: 'border-warning/50 focus:border-warning focus:ring-warning/20',
    error: 'border-error/50 focus:border-error focus:ring-error/20'
};

const disabledClasses = disabled ? 'opacity-50 cursor-not-allowed' : '';
const roundingClasses = 'rounded-lg';
</script>

<template>
    <div class="space-y-2">
        <!-- Label -->
        <label v-if="label" class="block text-sm font-medium text-base-content/80">
            {{ label }}
            <span v-if="required" class="text-error ml-1">*</span>
        </label>

        <!-- Input wrapper -->
        <div class="relative">
            <!-- Prefix -->
            <div 
                v-if="prefix"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-base-content/60 pointer-events-none z-10"
            >
                {{ prefix }}
            </div>

            <!-- Icon -->
            <div 
                v-if="icon"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-base-content/60 pointer-events-none z-10"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path 
                        v-if="icon === 'search'"
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                    <path 
                        v-else-if="icon === 'email'"
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    />
                    <path 
                        v-else-if="icon === 'lock'"
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                    />
                    <path 
                        v-else-if="icon === 'user'"
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                </svg>
            </div>

            <!-- Input -->
            <input
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :required="required"
                :class="[
                    baseClasses,
                    sizeClasses[size],
                    variantClasses[variant],
                    stateClasses[state],
                    roundingClasses,
                    disabledClasses,
                    { 'pl-10': icon || prefix },
                    { 'pr-10': suffix || clearable }
                ]"
                @input="handleInput"
                @focus="$emit('focus', $event)"
                @blur="$emit('blur', $event)"
            />

            <!-- Suffix -->
            <div 
                v-if="suffix"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-base-content/60 pointer-events-none z-10"
            >
                {{ suffix }}
            </div>

            <!-- Clear button -->
            <button
                v-if="clearable && modelValue"
                @click="handleClear"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full hover:bg-base-300/50 transition-colors duration-200 z-10"
                type="button"
            >
                <svg class="w-4 h-4 text-base-content/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- State indicator -->
            <div 
                v-if="state !== 'default'"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-10"
                :class="{ 'right-10': clearable && modelValue }"
            >
                <div 
                    :class="`w-5 h-5 rounded-full flex items-center justify-center ${
                        state === 'success' ? 'bg-success/20 text-success' :
                        state === 'warning' ? 'bg-warning/20 text-warning' :
                        'bg-error/20 text-error'
                    }`"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <!-- Helper text -->
        <p 
            v-if="helperText"
            :class="`text-sm ${
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