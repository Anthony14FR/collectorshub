<script setup>
import Button from '@/Components/UI/Button.vue';
import { Coins } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  userInventory: {
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
  }
});

const emit = defineEmits(['buy']);

const isAvatar = computed(() => props.item.type === 'avatar');
const isBackground = computed(() => props.item.type === 'background');

const isUnlocked = computed(() => {
  if (isAvatar.value || isBackground.value) {
    return props.userAvatars.includes(props.item.image) || props.userBackgrounds.includes(props.item.image);
  }
  return false;
});

const inventoryQuantity = computed(() => {
  const inventoryItem = props.userInventory.find(inv => inv.item_id === props.item.id);
  return inventoryItem ? inventoryItem.quantity : 0;
});

const formatPrice = (price) => {
  return price.toLocaleString();
};

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
  if (item.image) {
    return item.image;
  }
  return '/images/items/default-item.png';
};
</script>

<template>
  <div class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 group h-full flex flex-col">
    <div class="flex-1 flex flex-col">
      <div class="flex items-start gap-3 mb-3">
        <div :class="[
          'rounded-lg flex items-center justify-center p-1',
          isBackground ? 'w-32 h-24' : 'bg-gradient-to-br from-primary/20 to-secondary/20',
          isAvatar ? 'w-16 h-16' : 'w-12 h-12'
        ]">
          <img :src="getItemImage(item)" :alt="item.name" class="w-full h-full object-contain" :class="{ 'rounded-full': isAvatar }" />
        </div>
        <div class="flex-1">
          <h4 class="font-bold text-sm mb-1">{{ item.name }}</h4>
          <div class="flex items-center gap-2">
            <span :class="getRarityColor(item.rarity)" class="text-xs font-semibold capitalize">
              {{ item.rarity }}
            </span>
            <span class="text-xs text-base-content/50">{{ item.type }}</span>
          </div>
        </div>
      </div>

      <p class="text-xs text-base-content/70 mb-3 line-clamp-2">{{ item.description }}</p>

      <div class="flex items-center justify-between mb-3">
        <div class="text-xs text-base-content/60">
          <template v-if="isAvatar || isBackground">
            <span v-if="isUnlocked" class="text-green-500 font-semibold">Débloqué</span>
            <span v-else>Non débloqué</span>
          </template>
          <template v-else>
            Dans mon inventaire: 
            <span class="font-semibold">{{ inventoryQuantity }}</span>
          </template>
        </div>
        <div class="flex items-center gap-1">
          <Coins :size="14" class="text-warning" />
          <span class="font-bold text-warning">{{ formatPrice(item.price) }}</span>
        </div>
      </div>
    </div>

    <Button 
      v-if="!(isAvatar || isBackground) || !isUnlocked"
      @click="emit('buy', item)" 
      variant="primary" 
      size="sm" 
      class="w-full"
    >
      Acheter
    </Button>
    <Button 
      v-else
      variant="outline" 
      size="sm" 
      class="w-full !text-green-500 !border-green-500" 
      disabled
    >
      Déjà possédé
    </Button>
  </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-clamp: 2;
}
</style> 