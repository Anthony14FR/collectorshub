<script setup>
import { ref, computed } from 'vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  item: {
    type: Object,
    default: null
  },
  userCash: {
    type: Number,
    default: 0
  },
  processing: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'confirm']);

const quantity = ref(1);
const isAvatar = computed(() => props.item && props.item.type === 'avatar');
const isBackground = computed(() => props.item && props.item.type === 'background');
const isUnlockable = computed(() => isAvatar.value || isBackground.value);

const totalCost = computed(() => {
  if (!props.item) return 0;
  return props.item.price * quantity.value;
});

const canAfford = computed(() => {
  return props.userCash >= totalCost.value;
});

const remainingCash = computed(() => {
  return props.userCash - totalCost.value;
});

const confirmPurchase = () => {
  emit('confirm', {
    item_id: props.item.id,
    quantity: isUnlockable.value ? 1 : quantity.value
  });
};

const formatPrice = (price) => {
  return price.toLocaleString();
};

const getItemImage = (item) => {
  if (!item) return '';
  if (item.image) {
    return item.image;
  }
  return '/images/items/default-item.png';
};
</script>

<template>
  <Modal :show="show" @close="emit('close')" maxWidth="md" :fixedHeight="true">
    <template #header>
      <div v-if="item" class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center p-1">
          <img :src="getItemImage(item)" :alt="item?.name" class="w-full h-full object-contain" :class="{ 'rounded-full': isAvatar || isBackground }" />
        </div>
        <div class="flex flex-col">
          <h3 class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            Acheter {{ item?.name }}
          </h3>
          <div class="mt-1">
            <span class="text-sm font-semibold text-primary">
              {{ formatPrice(item?.price) }} ₽
            </span>
          </div>
        </div>
      </div>
    </template>

    <template #default>
      <div v-if="item" class="h-full flex flex-col">
        <div class="flex-1 space-y-4 overflow-y-auto">
          <div class="bg-base-200/30 rounded-xl p-4">
            <p class="text-sm text-base-content/80">
              {{ item.description }}
            </p>
          </div>
                    
          <div v-if="!isUnlockable" class="bg-base-200/30 rounded-xl p-4">
            <div class="flex items-center justify-between">
              <span>Quantité :</span>
              <div class="flex items-center gap-2">
                <Button
                  @click="quantity = Math.max(1, quantity - 1)"
                  variant="ghost"
                  size="xs"
                  :disabled="processing"
                >-</Button>
                <span class="font-bold text-lg">{{ quantity }}</span>
                <Button
                  @click="quantity = Math.min(99, quantity + 1)"
                  variant="ghost"
                  size="xs"
                  :disabled="processing"
                >+</Button>
              </div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-primary/10 to-secondary/10 rounded-xl p-4 border border-primary/20">
            <h4 class="font-bold text-sm mb-3">Récapitulatif</h4>
            <div class="space-y-2">
              <div class="flex items-center justify-between text-sm">
                <span class="text-base-content/70">Mon solde actuel</span>
                <span class="font-semibold">{{ formatPrice(userCash) }} ₽</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-base-content/70">Coût total</span>
                <span class="font-semibold text-warning">- {{ formatPrice(totalCost) }} ₽</span>
              </div>
              <div class="border-t border-base-300/50 pt-2">
                <div class="flex items-center justify-between">
                  <span class="text-base-content/70">Solde après achat</span>
                  <span :class="[
                    'font-bold text-lg',
                    canAfford ? 'text-success' : 'text-error'
                  ]">
                    {{ formatPrice(remainingCash) }} ₽
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="!canAfford" class="bg-error/10 text-error p-3 rounded-xl border border-error/20">
            <div class="flex items-center gap-2">
              <span>Solde insuffisant</span>
            </div>
          </div>
        </div>

        <div class="flex gap-3 mt-4">
          <Button
            @click="emit('close')"
            variant="outline"
            size="lg"
            class="flex-1"
            :disabled="processing"
          >
            Annuler
          </Button>

          <Button
            @click="confirmPurchase"
            variant="primary"
            size="lg"
            class="flex-1"
            :disabled="!canAfford || processing"
            :loading="processing"
          >
            {{ processing ? 'En cours...' : canAfford ? 'Confirmer' : 'Impossible' }}
          </Button>
        </div>
      </div>
    </template>
  </Modal>
</template>

<style scoped>
.fixed-height-modal :deep(.modal-content) {
    height: 500px;
}
</style> 