<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from 'vue';

interface ItemOption {
  id: number | string;
  name: string;
  image: string;
  type: string;
  rarity: string;
}

interface SelectedItem {
  id: number | string;
  quantity: number;
}

interface Props {
  options: ItemOption[];
  modelValue: SelectedItem[];
  placeholder?: string;
  maxHeight?: string;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Sélectionner des items...',
  maxHeight: 'max-h-64',
  disabled: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: SelectedItem[]): void;
}>();

const isOpen = ref(false);
const searchQuery = ref('');
const searchInput = ref<HTMLInputElement | null>(null);
const dropdownRef = ref<HTMLElement | null>(null);

const filteredOptions = computed(() => {
  if (!searchQuery.value.trim()) return props.options;
  const query = searchQuery.value.toLowerCase().trim();
  return props.options.filter(option =>
    option.name.toLowerCase().includes(query) ||
    option.type.toLowerCase().includes(query) ||
    option.rarity.toLowerCase().includes(query)
  );
});

const selectedItems = computed(() => props.modelValue);

const isSelected = (optionId: number | string) => {
  return props.modelValue.some(item => item.id === optionId);
};

const getQuantity = (optionId: number | string) => {
  return props.modelValue.find(item => item.id === optionId)?.quantity || 1;
};

const addOrUpdateItem = (option: ItemOption, quantity: number) => {
  if (props.disabled) return;
  let newValue = [...props.modelValue];
  const idx = newValue.findIndex(item => item.id === option.id);
  if (idx !== -1) {
    newValue[idx].quantity = quantity;
  } else {
    newValue.push({ id: option.id, quantity });
  }
  emit('update:modelValue', newValue);
};

const removeItem = (optionId: number | string) => {
  if (props.disabled) return;
  const newValue = props.modelValue.filter(item => item.id !== optionId);
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
  isOpen.value ? closeDropdown() : openDropdown();
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

const tempQuantities = ref<Record<string, number>>({});

const onQuantityInput = (option: ItemOption, value: string) => {
  let qty = parseInt(value, 10);
  if (isNaN(qty) || qty < 1) qty = 1;
  tempQuantities.value[option.id] = qty;
};

const onAddOrUpdate = (option: ItemOption) => {
  const qty = tempQuantities.value[option.id] || 1;
  addOrUpdateItem(option, qty);
  tempQuantities.value[option.id] = 1;
};

const getItemImage = (item: ItemOption) => {
  if (item.image) {
    if (item.image.startsWith('/')) return item.image;
    if (item.image.startsWith('images/items/')) return '/' + item.image;
    return '/images/items/' + item.image;
  }
  return '/images/items/default-item.png';
};
</script>

<template>
  <div ref="dropdownRef" class="relative w-full">
    <div @click="toggleDropdown" :class="[
      'min-h-[2.5rem] p-3 rounded-xl border-2 transition-all duration-200 cursor-pointer',
      'bg-base-100/60 backdrop-blur-sm',
      disabled
        ? 'border-base-300/30 cursor-not-allowed opacity-60'
        : isOpen
          ? 'border-primary/60 ring-2 ring-primary/20'
          : 'border-base-300/30 hover:border-primary/40'
    ]">
      <div class="flex flex-wrap gap-2 pr-16">
        <div v-for="item in selectedItems" :key="item.id"
             class="flex items-center gap-2 bg-primary/10 text-primary px-2 py-1 rounded-lg border border-primary/20">
          <img :src="getItemImage(options.find(o => o.id === item.id)!)" alt="" class="w-6 h-6 rounded border border-base-300" />
          <span class="text-xs font-medium truncate max-w-[120px]">{{ options.find(o => o.id === item.id)?.name }}</span>
          <span class="text-xs text-base-content/60">x{{ item.quantity }}</span>
          <button @click.stop="removeItem(item.id)" :disabled="disabled"
                  class="text-primary/60 hover:text-error transition-colors disabled:opacity-50">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div v-if="selectedItems.length === 0" class="text-base-content/50 text-sm py-1">
          {{ placeholder }}
        </div>
      </div>
      <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
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
          <input ref="searchInput" v-model="searchQuery" type="text" placeholder="Rechercher un item..."
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
          Aucun item trouvé
        </div>
        <div v-for="option in filteredOptions" :key="option.id" class="flex items-center gap-3 p-3 border-b border-base-300/10 last:border-b-0 hover:bg-primary/5">
          <img :src="getItemImage(option)" :alt="option.name" class="w-10 h-10 rounded border border-base-300" />
          <div class="flex-1 min-w-0">
            <div class="text-sm font-bold text-base-content truncate">{{ option.name }}</div>
            <div class="flex items-center gap-2 mt-0.5">
              <span class="text-xs text-base-content/60">{{ option.type }}</span>
              <span class="text-xs text-base-content/40">•</span>
              <span class="text-xs text-base-content/50">{{ option.rarity }}</span>
            </div>
          </div>
          <input type="number" min="1" :value="tempQuantities[option.id] ?? getQuantity(option.id)" @input="e => onQuantityInput(option, (e.target as HTMLInputElement)?.value)" class="w-16 px-2 py-1 border border-base-300 rounded-lg text-sm focus:border-primary/60 focus:ring-2 focus:ring-primary/20 transition-all" :disabled="isSelected(option.id)" />
          <button v-if="!isSelected(option.id)" @click="onAddOrUpdate(option)" class="ml-2 px-2 py-1 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/80 transition-colors">Ajouter</button>
          <span v-else class="ml-2 text-xs text-success font-bold">Ajouté</span>
        </div>
      </div>
    </div>
  </div>
</template> 