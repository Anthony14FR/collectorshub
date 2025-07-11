<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import FlashMessage from '@/Components/UI/FlashMessage.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';

const props = defineProps<{
  promoCodes?: any[];
  flash?: {
    type: 'success' | 'error';
    title: string;
    message: string;
    rewards?: any;
  };
  errors?: any;
}>();

const form = reactive({
  code: '',
  processing: false,
  errors: {} as Record<string, string>
});

const showValidating = ref(false);
const showSuccess = ref(false);
const showError = ref(false);
const customErrorTitle = ref('');
const customErrorMessage = ref('');
const isExiting = ref(false);

const showPackOpening = ref(false);
const currentItemIndex = ref(0);
const rewardItems = ref<any[]>([]);
const rewardCash = ref(0);
const collectedItems = ref<any[]>([]);
const currentDisplayItem = ref<any>(null);
const isItemAnimating = ref(false);

const getRarityColor = (rarity: string) => {
  switch (rarity) {
  case 'legendary': return 'from-yellow-400 via-orange-500 to-red-500';
  case 'epic': return 'from-purple-400 via-purple-500 to-indigo-600';
  case 'rare': return 'from-blue-400 via-blue-500 to-cyan-600';
  default: return 'from-gray-400 via-gray-500 to-gray-600';
  }
};

const getRarityGlow = (rarity: string) => {
  switch (rarity) {
  case 'legendary': return 'shadow-2xl shadow-yellow-500/50';
  case 'epic': return 'shadow-2xl shadow-purple-500/50';
  case 'rare': return 'shadow-xl shadow-blue-500/50';
  default: return 'shadow-lg shadow-gray-500/30';
  }
};

watch(() => props.flash, (newFlash) => {
  if (newFlash) {
    if (newFlash.type === 'success') {
      if (newFlash.rewards) {
        startPackOpening(newFlash.rewards);
      } else {
        showBackendMessage(newFlash.title, newFlash.message, 'success');
      }
    } else if (newFlash.type === 'error') {
      showBackendMessage(newFlash.title, newFlash.message, 'error');
    }
  }
}, { deep: true, immediate: true });


const startPackOpening = (rewards: any) => {
  rewardItems.value = rewards.items || [];
  rewardCash.value = rewards.cash || 0;
  collectedItems.value = [];
  currentDisplayItem.value = null;
  currentItemIndex.value = 0;
  isItemAnimating.value = false;
  showPackOpening.value = true;

  if (rewardItems.value.length > 0) {
    setTimeout(() => {
      showNextItem();
    }, 1000);
  }
};

const showNextItem = () => {
  if (currentItemIndex.value >= rewardItems.value.length || isItemAnimating.value) return;

  isItemAnimating.value = true;
  const item = rewardItems.value[currentItemIndex.value];

  currentDisplayItem.value = item;

  setTimeout(() => {
    collectedItems.value.push(item);
    currentDisplayItem.value = null;
    currentItemIndex.value++;
    isItemAnimating.value = false;

    if (currentItemIndex.value < rewardItems.value.length) {
      setTimeout(() => showNextItem(), 500);
    }
  }, 2500);
};

const closePackOpening = () => {
  const hasItems = collectedItems.value.length > 0;
  const hasCash = rewardCash.value > 0;

  showPackOpening.value = false;
  rewardItems.value = [];
  collectedItems.value = [];
  currentDisplayItem.value = null;
  currentItemIndex.value = 0;
  isItemAnimating.value = false;
  rewardCash.value = 0;

  if (hasItems || hasCash) {
    showBackendMessage('Récompenses Obtenues', 'Toutes vos récompenses ont été ajoutées à votre inventaire.', 'success');
  }
};

const showBackendMessage = (title: string, message: string, type: 'success' | 'error') => {
  resetMessages();
  customErrorTitle.value = title;
  customErrorMessage.value = message;

  if (type === 'success') {
    showSuccess.value = true;
    setTimeout(() => {
      hideWithAnimation(() => {
        showSuccess.value = false;
      });
    }, 4000);
  } else {
    showError.value = true;

    if (message.includes('already used')) {
      customErrorMessage.value = 'Code déjà utilisé';
    } else if (message.includes('expired')) {
      customErrorMessage.value = 'Ce code a expiré';
    } else if (message.includes('not available')) {
      customErrorMessage.value = 'Code non valide pour votre compte';
    } else if (message.includes('Invalid')) {
      customErrorMessage.value = 'Code invalide ou expiré';
    } else {
      customErrorMessage.value = message;
    }

    setTimeout(() => {
      hideWithAnimation(() => {
        resetMessages();
      });
    }, 5000);
  }
};

const hideWithAnimation = (callback: () => void) => {
  isExiting.value = true;
  setTimeout(() => {
    callback();
    isExiting.value = false;
  }, 300);
};

const submitCode = () => {
  resetMessages();
  form.processing = true;
  form.errors = {};
  showValidating.value = true;

  router.post('/promocodes/use', {
    code: form.code
  }, {
    preserveScroll: true,
    onSuccess: () => {
      form.processing = false;
      showValidating.value = false;
      form.code = '';
    },
    onError: (errors: any) => {
      form.processing = false;
      showValidating.value = false;
      showError.value = true;

      const errorMessage = Object.values(errors)[0] as string || 'Code invalide';
      if (errorMessage.includes('expired')) {
        customErrorMessage.value = 'Ce code a expiré';
      } else if (errorMessage.includes('already used')) {
        customErrorMessage.value = 'Code déjà utilisé';
      } else if (errorMessage.includes('not available')) {
        customErrorMessage.value = 'Code non valide pour votre compte';
      } else {
        customErrorMessage.value = errorMessage;
      }

      setTimeout(() => {
        hideWithAnimation(() => {
          resetMessages();
        });
      }, 4000);
    }
  });
};

const resetMessages = () => {
  showValidating.value = false;
  showSuccess.value = false;
  showError.value = false;
  customErrorMessage.value = '';
  isExiting.value = false;
};
</script>

<template>

  <Head title="Codes Promo" />

  <div data-theme="pokemon"
       class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects background-image="/images/section-me-background.jpg" />

    <Modal :show="showPackOpening" @close="closePackOpening" max-width="6xl">
      <template #header>
        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-gradient-to-br from-yellow-400/20 to-orange-500/20 rounded-lg flex items-center justify-center">
            <span class="text-lg">🎁</span>
          </div>
          <div class="flex flex-col">
            <h3
              class="text-xl font-bold bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 bg-clip-text text-transparent">
              Récompenses Débloquées
            </h3>
            <div class="mt-1" v-if="rewardItems.length > 0 || rewardCash > 0">
              <span class="text-sm font-semibold text-yellow-600">
                <span v-if="rewardItems.length > 0">{{ rewardItems.length }} items</span>
                <span v-if="rewardItems.length > 0 && rewardCash > 0"> • </span>
                <span v-if="rewardCash > 0">{{ rewardCash }} ₿ cash</span>
              </span>
            </div>
          </div>
        </div>
      </template>

      <template #default>
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div v-for="i in 15" :key="i"
               class="absolute w-2 h-2 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full animate-ping opacity-50"
               :style="{
                 top: Math.random() * 100 + '%',
                 left: Math.random() * 100 + '%',
                 animationDelay: Math.random() * 2 + 's'
               }">
          </div>
        </div>

        <div v-if="rewardCash > 0" class="text-center mb-6">
          <div
            class="bg-gradient-to-br from-green-400/20 to-emerald-500/20 backdrop-blur-lg border-2 border-green-400/40 rounded-2xl p-4 animate-bounce inline-block">
            <div class="flex items-center gap-3">
              <span class="text-3xl">💰</span>
              <div>
                <p class="text-lg font-bold text-green-400">Cash Gagné !</p>
                <p class="text-xl font-black text-white">+{{ rewardCash }} ₿</p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="rewardItems.length > 0"
             class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start min-h-[400px]">

          <div class="space-y-4">
            <h3 class="text-lg font-bold text-center text-base-content/80">
              Items Collectés ({{ collectedItems.length }})
            </h3>
            <div
              class="h-80 overflow-y-auto space-y-2 border-2 border-dashed border-base-300 rounded-xl p-3">
              <TransitionGroup enter-active-class="transition-all duration-500 ease-out"
                               enter-from-class="opacity-0 translate-x-4 scale-90"
                               enter-to-class="opacity-100 translate-x-0 scale-100">
                <div v-for="(item, index) in collectedItems" :key="'collected_' + item.id + '_' + index"
                     class="bg-gradient-to-br from-base-200/80 to-base-300/60 backdrop-blur-sm border-2 rounded-lg p-2"
                     :class="[
                       `border-gradient ${getRarityColor(item.rarity).replace('from-', 'border-').split(' ')[0]}/40`,
                       getRarityGlow(item.rarity)
                     ]">
                  <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br rounded-md flex items-center justify-center text-lg flex-shrink-0"
                         :class="getRarityColor(item.rarity)">
                      <span v-if="item.type === 'heal'">💊</span>
                      <span v-else-if="item.type === 'boost'">⚡</span>
                      <span v-else-if="item.type === 'evolution'">🔄</span>
                      <span v-else-if="item.type === 'ball'">⚪</span>
                      <span v-else-if="item.type === 'special'">✨</span>
                      <span v-else>📦</span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="font-bold text-xs bg-gradient-to-r bg-clip-text text-transparent truncate"
                          :class="getRarityColor(item.rarity)">
                        {{ item.name }}
                      </h4>
                      <span
                        class="inline-block bg-gradient-to-r text-white px-2 py-0.5 rounded-full text-xs font-bold"
                        :class="getRarityColor(item.rarity)">
                        x{{ item.quantity }}
                      </span>
                    </div>
                  </div>
                </div>
              </TransitionGroup>

              <div v-if="collectedItems.length === 0" class="text-center text-base-content/50 py-8">
                <p class="text-sm">Les items collectés apparaîtront ici</p>
              </div>
            </div>
          </div>

          <div class="flex flex-col items-center justify-center">
            <div class="text-center mb-4 w-full">
              <p class="text-lg font-bold text-base-content/80 mb-2">
                {{ currentItemIndex }} / {{ rewardItems.length }}
              </p>
              <div class="w-full bg-base-300 rounded-full h-2">
                <div class="bg-gradient-to-r from-primary to-secondary h-2 rounded-full transition-all duration-500"
                     :style="{ width: (currentItemIndex / rewardItems.length) * 100 + '%' }">
                </div>
              </div>
            </div>

            <Transition enter-active-class="transition-all duration-1000 ease-out"
                        enter-from-class="opacity-0 scale-50 rotate-180"
                        enter-to-class="opacity-100 scale-100 rotate-0"
                        leave-active-class="transition-all duration-500 ease-in"
                        leave-from-class="opacity-100 scale-100 rotate-0"
                        leave-to-class="opacity-0 scale-75 -translate-x-32">
              <div v-if="currentDisplayItem" :key="'display_' + currentDisplayItem.id"
                   class="bg-gradient-to-br from-base-100/95 to-base-200/90 backdrop-blur-lg border-4 rounded-3xl p-6 max-w-xs w-full transform transition-transform duration-300 relative"
                   :class="[
                     `border-gradient bg-gradient-to-r ${getRarityColor(currentDisplayItem.rarity)}`,
                     getRarityGlow(currentDisplayItem.rarity)
                   ]">
                <div class="absolute -top-2 -right-2 px-2 py-1 rounded-full text-xs font-bold text-white bg-gradient-to-r"
                     :class="getRarityColor(currentDisplayItem.rarity)">
                  {{ currentDisplayItem.rarity.toUpperCase() }}
                </div>

                <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br rounded-2xl flex items-center justify-center text-5xl"
                     :class="getRarityColor(currentDisplayItem.rarity)">
                  <span v-if="currentDisplayItem.type === 'heal'">💊</span>
                  <span v-else-if="currentDisplayItem.type === 'boost'">⚡</span>
                  <span v-else-if="currentDisplayItem.type === 'evolution'">🔄</span>
                  <span v-else-if="currentDisplayItem.type === 'ball'">⚪</span>
                  <span v-else-if="currentDisplayItem.type === 'special'">✨</span>
                  <span v-else>📦</span>
                </div>

                <h3 class="text-lg font-bold text-center mb-2 bg-gradient-to-r bg-clip-text text-transparent"
                    :class="getRarityColor(currentDisplayItem.rarity)">
                  {{ currentDisplayItem.name }}
                </h3>

                <p class="text-xs text-base-content/80 text-center mb-3">
                  {{ currentDisplayItem.description }}
                </p>

                <div class="text-center">
                  <span class="bg-gradient-to-r text-white px-3 py-1 rounded-full font-bold text-sm"
                        :class="getRarityColor(currentDisplayItem.rarity)">
                    x{{ currentDisplayItem.quantity }}
                  </span>
                </div>

                <div v-if="currentDisplayItem.rarity === 'legendary'"
                     class="absolute inset-0 pointer-events-none">
                  <div
                    class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 via-transparent to-orange-500/20 rounded-3xl animate-pulse">
                  </div>
                  <div class="absolute top-2 left-2 w-3 h-3 bg-yellow-400 rounded-full animate-ping">
                  </div>
                  <div
                    class="absolute bottom-2 right-2 w-2 h-2 bg-orange-500 rounded-full animate-ping delay-500">
                  </div>
                </div>
              </div>
            </Transition>

            <div v-if="!currentDisplayItem && currentItemIndex < rewardItems.length"
                 class="text-center text-base-content/60">
              <p class="text-sm">Préparation du prochain item...</p>
            </div>

            <div v-if="currentItemIndex >= rewardItems.length && !currentDisplayItem" class="text-center">
              <p class="text-lg font-bold text-success mb-2">🎉 Tous les items collectés !</p>
              <p class="text-sm text-base-content/70">Vous pouvez fermer cette fenêtre</p>
            </div>
          </div>

          <div class="space-y-4">
            <h3 class="text-lg font-bold text-center text-base-content/80">
              Items Restants ({{ Math.max(0, rewardItems.length - currentItemIndex) }})
            </h3>
            <div
              class="relative h-80 border-2 border-dashed border-base-300 rounded-xl p-3 overflow-hidden">
              <div
                class="relative w-full h-full flex flex-col-reverse items-center justify-end space-y-reverse space-y-1">
                <div v-for="(item, index) in rewardItems.slice(currentItemIndex)"
                     :key="'remaining_' + item.id + '_' + index"
                     class="w-12 h-12 bg-gradient-to-br rounded-lg flex items-center justify-center text-lg transform transition-all duration-300 opacity-70 hover:opacity-100"
                     :class="[
                       getRarityColor(item.rarity),
                       `translate-y-${Math.min(index * 2, 10)} scale-${Math.max(90 - index * 5, 70)}`
                     ]" :style="{
                       zIndex: rewardItems.length - index,
                       transform: `translateY(-${index * 8}px) scale(${Math.max(1 - index * 0.1, 0.7)})`
                     }">
                  <span class="text-lg">
                    <span v-if="item.type === 'heal'">💊</span>
                    <span v-else-if="item.type === 'boost'">⚡</span>
                    <span v-else-if="item.type === 'evolution'">🔄</span>
                    <span v-else-if="item.type === 'ball'">⚪</span>
                    <span v-else-if="item.type === 'special'">✨</span>
                    <span v-else>📦</span>
                  </span>
                </div>
              </div>

              <div v-if="currentItemIndex >= rewardItems.length"
                   class="absolute inset-0 flex items-center justify-center">
                <p class="text-sm text-base-content/50 text-center">Plus d'items à traiter</p>
              </div>
            </div>
          </div>
        </div>
      </template>
    </Modal>

    <FlashMessage :show="showSuccess || showError" :type="showSuccess ? 'success' : 'error'"
                  :title="customErrorTitle" :message="customErrorMessage" />

    <div class="h-full w-full flex items-center justify-center relative">

      <div class="absolute inset-0 z-[1]">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary/10 rounded-full blur-3xl animate-pulse">
        </div>
        <div
          class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-secondary/10 rounded-full blur-2xl animate-pulse delay-1000">
        </div>
        <div
          class="absolute top-1/2 right-1/3 w-32 h-32 bg-accent/10 rounded-full blur-xl animate-pulse delay-500">
        </div>
        <div class="absolute top-1/3 left-1/2 w-20 h-20 bg-info/5 rounded-full blur-lg animate-bounce">
        </div>
        <div
          class="absolute bottom-1/3 left-1/4 w-24 h-24 bg-success/8 rounded-full blur-2xl animate-pulse delay-700">
        </div>
      </div>

      <div class="absolute inset-0 pointer-events-none overflow-hidden z-[2]">
        <div class="absolute top-16 left-16 w-3 h-3 bg-primary rounded-full animate-pulse opacity-60"></div>
        <div
          class="absolute top-32 right-20 w-2 h-2 bg-secondary rounded-full animate-pulse delay-500 opacity-50">
        </div>
        <div
          class="absolute bottom-24 left-32 w-4 h-4 bg-accent rounded-full animate-pulse delay-1000 opacity-40">
        </div>
        <div
          class="absolute top-48 right-48 w-2.5 h-2.5 bg-info rounded-full animate-pulse delay-300 opacity-55">
        </div>
        <div
          class="absolute bottom-32 right-16 w-3 h-3 bg-success rounded-full animate-pulse delay-800 opacity-45">
        </div>
      </div>

      <div class="text-center z-10 max-w-lg mx-auto px-6">

        <div class="relative mb-4">
          <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black mb-3 relative z-10">
            <span
              class="bg-gradient-to-r from-success via-primary to-secondary bg-clip-text text-transparent drop-shadow-2xl animate-pulse">
              🎟️ CODES PROMO
            </span>
          </h2>

          <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
              class="absolute top-4 left-8 w-3 h-3 bg-accent rounded-full blur-sm animate-pulse opacity-40">
            </div>
            <div
              class="absolute top-12 right-12 w-2 h-2 bg-info rounded-full blur-sm animate-pulse opacity-50 delay-500">
            </div>
            <div
              class="absolute bottom-8 left-16 w-4 h-4 bg-secondary rounded-full blur-md animate-pulse opacity-30 delay-1000">
            </div>
            <div
              class="absolute top-8 right-24 w-2.5 h-2.5 bg-primary rounded-full blur-sm animate-pulse opacity-45 delay-700">
            </div>
          </div>
        </div>

        <h3 class="text-lg sm:text-xl md:text-2xl font-black text-base-content/90 mb-3">
          EXCLUSIVES
        </h3>

        <div class="flex items-center justify-center gap-4 mb-4">
          <div class="h-px bg-gradient-to-r from-transparent via-primary/60 to-primary/20 w-16 md:w-24">
          </div>
          <div
            class="w-3 h-3 bg-gradient-to-br from-primary to-secondary rounded-full shadow-lg shadow-primary/50 animate-pulse">
          </div>
          <div class="h-px bg-gradient-to-l from-transparent via-primary/60 to-primary/20 w-16 md:w-24">
          </div>
        </div>

        <div
          class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 rounded-3xl p-6 relative overflow-hidden shadow-2xl shadow-primary/10">
          <div class="absolute top-6 right-6 w-6 h-6 border-2 border-success/30 rounded-full animate-pulse">
          </div>
          <div
            class="absolute bottom-6 left-6 w-4 h-4 border-2 border-primary/40 rounded-full animate-pulse delay-500">
          </div>
          <div
            class="absolute top-8 left-8 w-2 h-2 bg-accent rounded-full blur-sm animate-pulse opacity-60 delay-1000">
          </div>
          <div
            class="absolute bottom-8 right-8 w-3 h-3 bg-info rounded-full blur-sm animate-pulse opacity-50 delay-300">
          </div>

          <div class="text-center mb-5">
            <div
              class="w-12 h-12 bg-gradient-to-br from-success/20 to-success/40 rounded-full flex items-center justify-center mb-3 mx-auto">
              <span class="text-xl">✨</span>
            </div>
            <h3
              class="text-lg md:text-xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-1">
              Entrer un Code Promo
            </h3>
            <p class="text-sm text-base-content/70">Saisis ton code pour débloquer tes récompenses</p>
          </div>

          <form @submit.prevent="submitCode" class="space-y-4">
            <div>
              <label for="code"
                     class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-2">
                Code Promo *
              </label>
              <input id="code" v-model="form.code" type="text" placeholder="WELCOME2024"
                     class="w-full px-4 py-3 bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 text-center font-mono text-lg uppercase focus:outline-none"
                     :class="{ 'border-error/50 focus:border-error': form.errors.code }" required />
              <div v-if="form.errors.code" class="mt-1 text-xs text-error">
                {{ form.errors.code }}
              </div>
              <p v-else class="mt-1 text-xs text-base-content/60">
                Entre ton code promo ici
              </p>
            </div>

            <Button type="submit" variant="primary" size="lg" icon="lightning"
                    :disabled="form.processing || !form.code" class="w-full">
              <span v-if="form.processing">Validation en cours...</span>
              <span v-else>✨ Utiliser le Code</span>
            </Button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-mono {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

input[type="text"] {
    text-transform: uppercase;
}
</style>
