<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from 'vue';

interface Option {
  id: number | string;
  name: string;
  email?: string;
  description?: string;
  avatar?: string;
}

interface Props {
  options: Option[];
  modelValue: (number | string)[];
  placeholder?: string;
  searchPlaceholder?: string;
  emptyText?: string;
  maxHeight?: string;
  disabled?: boolean;
  maxDisplayItems?: number;
  showField?: 'name' | 'email';
}

interface Emits {
  (e: 'update:modelValue', value: (number | string)[]): void;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Sélectionner des éléments...',
  searchPlaceholder: 'Rechercher...',
  emptyText: 'Aucun élément trouvé',
  maxHeight: 'max-h-64',
  disabled: false,
  maxDisplayItems: 2,
  showField: 'email',
});

const emit = defineEmits<Emits>();

const isOpen = ref(false);
const searchQuery = ref('');
const searchInput = ref<HTMLInputElement | null>(null);
const dropdownRef = ref<HTMLElement | null>(null);

const selectedOptions = computed(() => {
  return props.options.filter(option => props.modelValue.includes(option.id));
});

const filteredOptions = computed(() => {
  if (!searchQuery.value.trim()) return props.options;

  const query = searchQuery.value.toLowerCase().trim();
  return props.options.filter(option => {
    const searchFields = [
      option.name,
      option.email,
      option.description
    ].filter(Boolean).map(field => field!.toLowerCase());

    return searchFields.some(field => field.includes(query));
  });
});

const displayedSelectedItems = computed(() => {
  const items = selectedOptions.value;
  if (items.length <= props.maxDisplayItems) {
    return {
      visible: items,
      hidden: 0
    };
  }

  return {
    visible: items.slice(0, props.maxDisplayItems),
    hidden: items.length - props.maxDisplayItems
  };
});

const getDisplayText = (option: Option) => {
  if (props.showField === 'email' && option.email) {
    return option.email;
  }
  return option.name;
};

const isSelected = (optionId: number | string) => {
  return props.modelValue.includes(optionId);
};

const toggleOption = (optionId: number | string) => {
  if (props.disabled) return;

  const newValue = isSelected(optionId)
    ? props.modelValue.filter(id => id !== optionId)
    : [...props.modelValue, optionId];

  emit('update:modelValue', newValue);
};

const removeOption = (optionId: number | string) => {
  if (props.disabled) return;

  const newValue = props.modelValue.filter(id => id !== optionId);
  emit('update:modelValue', newValue);
};

const openDropdown = () => {
  if (props.disabled) return;

  isOpen.value = true;
  searchQuery.value = '';

  setTimeout(() => {
    searchInput.value?.focus();
  }, 100);
};

const closeDropdown = () => {
  isOpen.value = false;
  searchQuery.value = '';
};

const toggleDropdown = (event?: Event) => {
  if (props.disabled) return;

  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }

  if (isOpen.value) {
    closeDropdown();
  } else {
    openDropdown();
  }
};

const handleContainerClick = () => {
  if (props.disabled) return;

  if (!isOpen.value) {
    openDropdown();
  }
};

const clearAll = () => {
  if (props.disabled) return;
  emit('update:modelValue', []);
};

const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement;

  if (dropdownRef.value && !dropdownRef.value.contains(target)) {
    closeDropdown();
  }
};

watch(isOpen, (newValue) => {
  document.removeEventListener('click', handleClickOutside);

  if (newValue) {
    setTimeout(() => {
      document.addEventListener('click', handleClickOutside, { capture: false });
    }, 100);
  }
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div ref="dropdownRef" class="multiple-select-dropdown relative w-full">
    <div @click="handleContainerClick" :class="[
      'min-h-[2.5rem] p-3 rounded-xl border-2 transition-all duration-200 cursor-pointer',
      'bg-base-100/60 backdrop-blur-sm',
      disabled
        ? 'border-base-300/30 cursor-not-allowed opacity-60'
        : isOpen
          ? 'border-primary/60 ring-2 ring-primary/20'
          : 'border-base-300/30 hover:border-primary/40'
    ]">
      <div class="flex flex-wrap gap-2 pr-16">
        <div v-for="option in displayedSelectedItems.visible" :key="option.id"
             class="flex items-center gap-2 bg-primary/10 text-primary px-2 py-1 rounded-lg border border-primary/20">
          <div class="flex items-center gap-2">
            <div v-if="option.avatar"
                 class="w-4 h-4 rounded-full bg-primary/20 flex items-center justify-center">
              <span class="text-xs">{{ option.name.charAt(0).toUpperCase() }}</span>
            </div>
            <span class="text-xs font-medium truncate max-w-[120px]">{{ getDisplayText(option) }}</span>
          </div>
          <button @click.stop="removeOption(option.id)" :disabled="disabled"
                  class="text-primary/60 hover:text-primary transition-colors disabled:opacity-50">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="displayedSelectedItems.hidden > 0"
             class="flex items-center gap-1 bg-secondary/10 text-secondary px-2 py-1 rounded-lg border border-secondary/20">
          <span class="text-xs font-medium">+{{ displayedSelectedItems.hidden }}</span>
        </div>

        <div v-if="selectedOptions.length === 0" class="text-base-content/50 text-sm py-1">
          {{ placeholder }}
        </div>
      </div>

      <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
        <button v-if="selectedOptions.length > 0 && !disabled" @click.stop="clearAll"
                class="text-base-content/40 hover:text-error transition-colors p-1" title="Tout supprimer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
            </path>
          </svg>
        </button>

        <button @click.stop="toggleDropdown($event)"
                class="text-base-content/40 hover:text-base-content/60 transition-colors p-1">
          <svg :class="['w-4 h-4 transition-transform duration-200', isOpen ? 'rotate-180' : '']" fill="none"
               stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
      </div>
    </div>

    <div v-if="isOpen" :class="[
      'absolute top-full left-0 right-0 mt-2 z-50',
      'bg-base-100/95 backdrop-blur-sm rounded-xl border-2 border-primary/20',
      'shadow-2xl shadow-primary/10 overflow-hidden'
    ]">
      <div class="p-3 border-b border-base-300/20">
        <div class="relative">
          <input ref="searchInput" v-model="searchQuery" type="text" :placeholder="searchPlaceholder"
                 class="w-full pl-9 pr-3 py-2 bg-base-200/50 border border-base-300/30 rounded-lg text-sm focus:border-primary/60 focus:ring-2 focus:ring-primary/20 transition-all" />
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-base-content/40">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div :class="['overflow-y-auto', maxHeight]">
        <div v-if="filteredOptions.length === 0" class="p-4 text-center text-base-content/50 text-sm">
          {{ emptyText }}
        </div>

        <div v-for="option in filteredOptions" :key="option.id" @click="toggleOption(option.id)" :class="[
          'flex items-center gap-3 p-3 cursor-pointer transition-all duration-150',
          'hover:bg-primary/5 border-b border-base-300/10 last:border-b-0',
          isSelected(option.id) ? 'bg-primary/10' : ''
        ]">
          <div :class="[
            'w-4 h-4 rounded border-2 flex items-center justify-center transition-all',
            isSelected(option.id)
              ? 'bg-primary border-primary text-primary-content'
              : 'border-base-300/60 hover:border-primary/40'
          ]">
            <svg v-if="isSelected(option.id)" class="w-3 h-3" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
              </path>
            </svg>
          </div>

          <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
            <span class="text-sm font-medium text-primary">{{ option.name.charAt(0).toUpperCase() }}</span>
          </div>

          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-base-content truncate">{{ option.name }}</div>
            <div class="flex items-center gap-2 mt-0.5">
              <div v-if="option.email" class="text-xs text-base-content/60 truncate">{{ option.email }}
              </div>
              <span v-if="option.email && option.description"
                    class="text-xs text-base-content/40">•</span>
              <div v-if="option.description" class="text-xs text-base-content/50 truncate">{{
                option.description.split('•')[1]?.trim() || option.description }}</div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="selectedOptions.length > 0" class="p-3 bg-primary/5 border-t border-primary/20">
        <div class="text-xs text-primary font-medium text-center">
          {{ selectedOptions.length }} élément(s) sélectionné(s)
        </div>
      </div>
    </div>
  </div>
</template>