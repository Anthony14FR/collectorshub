<script setup>
import { ref, computed, watch } from 'vue';
import ShopItemCard from '@/Components/Cards/ShopItemCard.vue';
import Button from '@/Components/UI/Button.vue';

const props = defineProps({
  items: {
    type: Array,
    required: true
  },
  inventory: {
    type: Array,
    default: () => []
  },
  userAvatars: {
    type: Array,
    default: () => []
  },
  userBackgrounds: {
    type: Array,
    default: () => []
  },
  activeTab: {
    type: String,
    default: 'balls'
  }
});

const emit = defineEmits(['buy', 'changeTab']);

const currentPage = ref(1);
const itemsPerPage = ref(9);

watch(() => props.activeTab, () => {
  currentPage.value = 1;
});

const filteredItems = computed(() => {
  let result = props.items;
    
  if (props.activeTab === 'balls') {
    result = result.filter(item => item.type === 'ball');
  } else if (props.activeTab === 'avatars') {
    result = result.filter(item => item.type === 'avatar');
  } else if (props.activeTab === 'backgrounds') {
    result = result.filter(item => item.type === 'background');
  }
    
  return result;
});

const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  const items = filteredItems.value.slice(start, end);
    
  const paddedItems = [...items];
  while (paddedItems.length < itemsPerPage.value && paddedItems.length < 9) {
    paddedItems.push({ id: `placeholder-${paddedItems.length}`, isPlaceholder: true });
  }
    
  return paddedItems;
});

const totalPages = computed(() => {
  return Math.ceil(filteredItems.value.length / itemsPerPage.value);
});

const changePage = (page) => {
  currentPage.value = page;
};

const paginationItems = computed(() => {
  const maxVisiblePages = window.innerWidth < 640 ? 5 : 7;
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
    
  if (total <= maxVisiblePages) {
    for (let i = 1; i <= total; i++) {
      pages.push({ type: 'page', value: i });
    }
  } else {
    let startPage = Math.max(1, current - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(total, startPage + maxVisiblePages - 1);
        
    if (endPage - startPage < maxVisiblePages - 1) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
        
    if (startPage > 1) {
      pages.push({ type: 'page', value: 1 });
      if (startPage > 2) {
        pages.push({ type: 'ellipsis' });
      }
    }
        
    for (let i = startPage; i <= endPage; i++) {
      if (i !== 1 && i !== total) {
        pages.push({ type: 'page', value: i });
      }
    }
        
    if (endPage < total) {
      if (endPage < total - 1) {
        pages.push({ type: 'ellipsis' });
      }
      pages.push({ type: 'page', value: total });
    }
        
    if (!pages.some(p => p.value === 1) && startPage === 1) {
      pages.unshift({ type: 'page', value: 1 });
    }
    if (!pages.some(p => p.value === total) && endPage === total) {
      pages.push({ type: 'page', value: total });
    }
  }
    
  return pages;
});
</script>

<template>
  <div class="flex flex-col h-full">
    <div class="shrink-0 p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
      <div class="flex flex-wrap gap-2 sm:gap-4 justify-center sm:justify-start">
        <button
          @click="emit('changeTab', 'balls')"
          :class="[
            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200',
            activeTab === 'balls' 
              ? 'bg-warning text-warning-content shadow-md' 
              : 'text-base-content/70 hover:text-base-content hover:bg-base-200/50'
          ]"
        >
          Balls
        </button>
        <button
          @click="emit('changeTab', 'avatars')"
          :class="[
            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200',
            activeTab === 'avatars' 
              ? 'bg-warning text-warning-content shadow-md' 
              : 'text-base-content/70 hover:text-base-content hover:bg-base-200/50'
          ]"
        >
          Avatars
        </button>
        <button
          @click="emit('changeTab', 'backgrounds')"
          :class="[
            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200',
            activeTab === 'backgrounds' 
              ? 'bg-warning text-warning-content shadow-md' 
              : 'text-base-content/70 hover:text-base-content hover:bg-base-200/50'
          ]"
        >
          Backgrounds
        </button>
      </div>
    </div>

    <div class="flex-1 overflow-y-auto p-2 sm:p-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-2 sm:gap-4">
        <template v-for="item in paginatedItems" :key="item.id">
          <ShopItemCard
            v-if="!item.isPlaceholder"
            :item="item"
            :userInventory="inventory"
            :userAvatars="userAvatars"
            :userBackgrounds="userBackgrounds"
            @buy="emit('buy', $event)"
          />
          <div v-else class="bg-base-200/30 rounded-xl border border-base-300/20 h-full min-h-[180px] sm:min-h-[200px]"></div>
        </template>
      </div>
    </div>

    <div v-if="totalPages > 1" class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-2 sm:px-4 py-3 border-t border-warning/20">
      <div class="flex justify-center items-center gap-1">
        <Button
          @click="changePage(1)"
          :disabled="currentPage === 1"
          variant="ghost"
          size="sm"
          title="Première page"
          class="hidden sm:flex"
        >
          ««
        </Button>
        <Button
          @click="changePage(currentPage - 1)"
          :disabled="currentPage === 1"
          variant="ghost"
          size="sm"
          title="Page précédente"
          class="w-8 h-8 p-0 flex items-center justify-center"
        >
          ‹
        </Button>

        <template v-for="(item, index) in paginationItems" :key="index">
          <Button
            v-if="item.type === 'page'"
            @click="changePage(item.value)"
            :variant="currentPage === item.value ? 'primary' : 'ghost'"
            size="sm"
            class="w-8 h-8 p-0 flex items-center justify-center text-xs sm:text-sm"
          >
            {{ item.value }}
          </Button>
          <span v-else class="text-base-content/50 px-1 text-sm">...</span>
        </template>

        <Button
          @click="changePage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          variant="ghost"
          size="sm"
          title="Page suivante"
          class="w-8 h-8 p-0 flex items-center justify-center"
        >
          ›
        </Button>
        <Button
          @click="changePage(totalPages)"
          :disabled="currentPage === totalPages"
          variant="ghost"
          size="sm"
          title="Dernière page"
          class="hidden sm:flex"
        >
          »»
        </Button>
      </div>
      
      <div class="flex justify-center mt-2">
        <span class="text-xs text-base-content/70">
          Page {{ currentPage }} sur {{ totalPages }}
        </span>
      </div>
    </div>
  </div>
</template>