<script setup>
import { computed } from 'vue';
import Modal from '@/Components/UI/Modal.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  inventory: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['close']);

const getRarityColor = (rarity) => {
  switch (rarity) {
  case 'normal': return 'text-gray-600';
  case 'rare': return 'text-blue-400';
  case 'epic': return 'text-purple-400';
  case 'legendary': return 'text-yellow-400';
  default: return 'text-gray-600';
  }
};

const getItemImage = (item) => {
  if (!item) return '';
  if (item.image) {
    return item.image;
  }
  return '/images/items/default-item.png';
};

const sortedInventory = computed(() => {
  return [...props.inventory].sort((a, b) => {
    if (a.item.type !== b.item.type) {
      return a.item.type.localeCompare(b.item.type);
    }
    const rarityOrder = { legendary: 0, epic: 1, rare: 2, normal: 3 };
    const rarityA = rarityOrder[a.item.rarity] || 99;
    const rarityB = rarityOrder[b.item.rarity] || 99;
    if (rarityA !== rarityB) {
      return rarityA - rarityB;
    }
    return a.item.name.localeCompare(b.item.name);
  });
});
</script>

<template>
  <Modal :show="show" @close="emit('close')" maxWidth="4xl" :fixedHeight="true">
    <template #header>
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-success/20 to-success/40 rounded-lg flex items-center justify-center">
          <span class="text-lg">📦</span>
        </div>
        <div class="flex flex-col">
          <h3 class="text-xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent">
            Mon Inventaire
          </h3>
          <div class="mt-1">
            <span class="text-sm font-semibold text-success">{{ inventory.length }} types d'items</span>
          </div>
        </div>
      </div>
    </template>

    <template #default>
      <div v-if="sortedInventory.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto p-2">
        <div v-for="inventoryItem in sortedInventory" :key="inventoryItem.id" class="bg-base-200/30 backdrop-blur-sm rounded-xl p-3 border border-base-300/20">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center p-1">
              <img :src="getItemImage(inventoryItem.item)" :alt="inventoryItem.item.name" class="w-full h-full object-contain" :class="{ 'rounded-full': inventoryItem.item.type === 'avatar' }" />
            </div>
            <div class="flex-1">
              <h4 class="font-bold text-xs">{{ inventoryItem.item.name }}</h4>
              <div class="flex items-center gap-2">
                <span :class="getRarityColor(inventoryItem.item.rarity)" class="text-xs font-semibold capitalize">
                  {{ inventoryItem.item.rarity }}
                </span>
                <span class="text-xs text-base-content/50">{{ inventoryItem.item.type }}</span>
              </div>
            </div>
          </div>

          <div class="text-center">
            <div class="text-lg font-bold text-success">
              {{ inventoryItem.quantity }}
            </div>
            <div class="text-xs text-base-content/70">
              exemplaires
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-8">
        <p class="text-2xl mb-2">📦</p>
        <p class="text-sm mb-1">Inventaire vide</p>
        <p class="opacity-70 text-xs">Achetez des items pour les voir ici</p>
      </div>
    </template>
  </Modal>
</template> 