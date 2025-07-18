<script setup lang="ts">
import Alert from '@/Components/UI/Alert.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Props {
  promoCodes?: any[];
  auth?: {
    user: {
      id: number;
      username: string;
      cash: number;
    };
  };
  flash?: {
    type?: 'success' | 'error';
    title?: string;
    message?: string;
    rewards?: {
      cash?: number;
      items?: any[];
    };
  };
  errors?: {
    code?: string;
    message?: string;
  };
}

const props = defineProps<Props>();

const form = ref({
  code: '',
  processing: false,
  errors: {} as Record<string, string>
});

const showAlert = ref(false);
const alertType = ref<'success' | 'error'>('success');
const alertTitle = ref('');
const alertMessage = ref('');
const showRewardsModal = ref(false);
const rewards = ref<any>(null);

const formatCash = (amount: number) => {
  return amount.toLocaleString();
};

const getRarityColor = (rarity: string) => {
  const colors = {
    'normal': 'text-base-content',
    'rare': 'text-info',
    'epic': 'text-secondary',
    'legendary': 'text-warning',
  };
  return colors[rarity as keyof typeof colors] || 'text-base-content';
};

const getRarityBg = (rarity: string) => {
  const colors = {
    'normal': 'bg-base-300/20',
    'rare': 'bg-info/20',
    'epic': 'bg-secondary/20',
    'legendary': 'bg-warning/20',
  };
  return colors[rarity as keyof typeof colors] || 'bg-base-300/20';
};

const totalRewards = computed(() => {
  if (!rewards.value) return 0;

  let count = 0;
  if (rewards.value.cash && rewards.value.cash > 0) count++;
  if (rewards.value.items && rewards.value.items.length > 0) count += rewards.value.items.length;

  return count;
});

const submitCode = () => {
  form.value.processing = true;
  form.value.errors = {};

  router.post('/promocodes/use', {
    code: form.value.code
  }, {
    preserveScroll: true,
    onSuccess: () => {
      form.value.code = '';
      form.value.processing = false;
    },
    onError: (errors: Record<string, string>) => {
      form.value.errors = errors;
      form.value.processing = false;

      showAlert.value = true;
      alertType.value = 'error';
      alertTitle.value = 'Erreur de validation';
      alertMessage.value = errors.code || errors.message || 'Une erreur est survenue';

      setTimeout(() => {
        showAlert.value = false;
      }, 4000);
    },
    onFinish: () => {
      form.value.processing = false;
    }
  });
};

const closeRewardsModal = () => {
  showRewardsModal.value = false;
  rewards.value = null;
};

watch(() => props.flash, (newFlash) => {
  if (newFlash?.type) {
    if (newFlash.type === 'success') {
      if (newFlash.rewards) {
        rewards.value = newFlash.rewards;
        showRewardsModal.value = true;
      } else {
        showAlert.value = true;
        alertType.value = 'success';
        alertTitle.value = newFlash.title || 'Succès';
        alertMessage.value = newFlash.message || '';

        setTimeout(() => {
          showAlert.value = false;
        }, 4000);
      }
    } else {
      showAlert.value = true;
      alertType.value = 'error';
      alertTitle.value = newFlash.title || 'Erreur';
      alertMessage.value = newFlash.message || '';

      setTimeout(() => {
        showAlert.value = false;
      }, 4000);
    }
  }
}, { immediate: true, deep: true });
</script>

<template>

  <Head title="Codes Promo" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-y-auto">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1
            class="text-2xl font-bold bg-gradient-to-r from-success to-primary bg-clip-text text-transparent mb-1 tracking-wider">
            🎟️ CODES PROMO
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            RÉCOMPENSES EXCLUSIVES
          </p>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[800px] max-h-[600px]">
        <Transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in" leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0">
          <div v-if="showAlert" class="mb-4">
            <Alert :type="alertType" :title="alertTitle" :message="alertMessage" :dismissible="true"
                   @dismiss="showAlert = false" />
          </div>
        </Transition>

        <div
          class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-success/10 to-primary/5 border-b border-success/20">
            <div class="flex justify-between items-center">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">✨</span>
                UTILISER UN CODE
              </h3>
              <div class="flex items-center gap-2">
                <div class="text-xs text-base-content/70">Solde:</div>
                <div class="text-xs font-bold text-primary">{{ formatCash(props.auth?.user?.cash || 0) }} 💰</div>
              </div>
            </div>
          </div>

          <div class="flex-1 p-6 overflow-y-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-full">
              <div class="space-y-6">
                <div
                  class="bg-gradient-to-br from-base-200/60 to-base-300/40 backdrop-blur-lg rounded-2xl p-6 border border-success/20">
                  <div class="text-center mb-6">
                    <div
                      class="w-16 h-16 bg-gradient-to-br from-success/20 to-primary/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                      <span class="text-3xl">🎟️</span>
                    </div>
                    <h2 class="text-xl font-bold mb-2">
                      <span class="bg-gradient-to-r from-success to-primary bg-clip-text text-transparent">
                        Code Promotionnel
                      </span>
                    </h2>
                    <p class="text-sm text-base-content/70">
                      Entrez votre code pour débloquer vos récompenses
                    </p>
                  </div>

                  <form @submit.prevent="submitCode" class="space-y-4">
                    <div>
                      <label for="code" class="block text-sm font-semibold text-base-content/80 mb-2">
                        Code
                      </label>
                      <Input id="code" v-model="form.code" type="text" placeholder="Entrez votre code"
                             class="text-center font-mono text-lg uppercase tracking-widest"
                             :class="{ 'border-error': form.errors.code }" required :disabled="form.processing" />
                      <div v-if="form.errors.code" class="mt-1 text-xs text-error">
                        {{ form.errors.code }}
                      </div>
                    </div>

                    <Button type="submit" variant="primary" size="lg" class="w-full"
                            :disabled="form.processing || !form.code.trim()">
                      <span v-if="form.processing" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                          </circle>
                          <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                          </path>
                        </svg>
                        Validation...
                      </span>
                      <span v-else class="flex items-center gap-2">
                        ✨ Utiliser
                      </span>
                    </Button>
                  </form>
                </div>

                <div
                  class="bg-gradient-to-br from-base-200/60 to-base-300/40 backdrop-blur-lg rounded-xl p-4 border border-info/20">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-info/20 rounded-lg flex items-center justify-center">
                      <span class="text-lg">💡</span>
                    </div>
                    <h3 class="font-semibold text-info text-sm">Comment ça marche ?</h3>
                  </div>
                  <p class="text-xs text-base-content/70">
                    Les codes promo vous permettent d'obtenir de l'argent, des objets et d'autres récompenses
                    gratuitement.
                  </p>
                </div>
              </div>

              <div class="space-y-6">
                <div
                  class="bg-gradient-to-br from-base-200/60 to-base-300/40 backdrop-blur-lg rounded-xl p-4 border border-warning/20">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-warning/20 rounded-lg flex items-center justify-center">
                      <span class="text-lg">⚠️</span>
                    </div>
                    <h3 class="font-semibold text-warning text-sm">Important</h3>
                  </div>
                  <p class="text-xs text-base-content/70">
                    Chaque code ne peut être utilisé qu'une seule fois par compte. Vérifiez bien votre saisie.
                  </p>
                </div>

                <div
                  class="bg-gradient-to-br from-base-200/60 to-base-300/40 backdrop-blur-lg rounded-xl p-4 border border-accent/20">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-accent/20 rounded-lg flex items-center justify-center">
                      <span class="text-lg">🎁</span>
                    </div>
                    <h3 class="font-semibold text-accent text-sm">Types de récompenses</h3>
                  </div>
                  <div class="space-y-2">
                    <div class="flex items-center gap-2 text-xs">
                      <span class="w-2 h-2 bg-primary rounded-full"></span>
                      <span class="text-base-content/70">PokéCoins</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                      <span class="w-2 h-2 bg-secondary rounded-full"></span>
                      <span class="text-base-content/70">Objets rares</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                      <span class="w-2 h-2 bg-accent rounded-full"></span>
                      <span class="text-base-content/70">Avatars exclusifs</span>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-gradient-to-br from-base-200/60 to-base-300/40 backdrop-blur-lg rounded-xl p-4 border border-primary/20">
                  <div class="text-center">
                    <div
                      class="w-12 h-12 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                      <span class="text-2xl">💰</span>
                    </div>
                    <h3 class="font-semibold text-sm mb-1">Mon Solde</h3>
                    <div class="text-2xl font-bold text-primary">
                      {{ formatCash(props.auth?.user?.cash || 0) }}
                    </div>
                    <div class="text-xs text-base-content/70">PokéCoins</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showRewardsModal" @close="closeRewardsModal" max-width="lg">
      <template #header>
        <div class="flex items-center gap-3">
          <div
            class="w-10 h-10 bg-gradient-to-br from-success/20 to-primary/20 rounded-xl flex items-center justify-center">
            <span class="text-2xl">🎉</span>
          </div>
          <div>
            <h3 class="text-xl font-bold">
              <span class="bg-gradient-to-r from-success to-primary bg-clip-text text-transparent">
                Félicitations !
              </span>
            </h3>
            <p class="text-sm text-base-content/70">
              Code utilisé avec succès
            </p>
          </div>
        </div>
      </template>

      <template #default>
        <div v-if="rewards" class="space-y-6">
          <div
            class="text-center p-6 bg-gradient-to-br from-success/10 to-primary/5 rounded-xl border border-success/20">
            <div
              class="w-16 h-16 bg-gradient-to-br from-success/20 to-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">✨</span>
            </div>
            <h4 class="text-lg font-bold text-success mb-2">Récompenses obtenues !</h4>
            <p class="text-sm text-base-content/70">
              {{ totalRewards }} récompense{{ totalRewards > 1 ? 's' : '' }} ajoutée{{ totalRewards > 1 ? 's' : '' }} à
              votre compte
            </p>
          </div>

          <div class="grid grid-cols-1 gap-4">
            <div v-if="rewards.cash && rewards.cash > 0"
                 class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-sm rounded-xl p-4 border border-base-300/30">
              <div class="flex items-center gap-4">
                <div
                  class="w-12 h-12 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-xl flex items-center justify-center">
                  <span class="text-2xl">💰</span>
                </div>
                <div class="flex-1">
                  <h5 class="font-semibold">PokéCoins</h5>
                  <p class="text-sm text-base-content/70">Argent ajouté à votre solde</p>
                </div>
                <div class="text-right">
                  <div class="text-xl font-bold text-primary">
                    +{{ formatCash(rewards.cash) }}
                  </div>
                </div>
              </div>
            </div>

            <div v-if="rewards.items && rewards.items.length > 0" class="space-y-3">
              <h5 class="font-semibold text-base-content/80">Objets obtenus :</h5>

              <div v-for="item in rewards.items" :key="item.id"
                   class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-sm rounded-xl p-4 border border-base-300/30">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-xl overflow-hidden bg-base-200/50 flex items-center justify-center">
                    <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-8 h-8 object-contain"
                         style="image-rendering: pixelated;" />
                    <span v-else class="text-xl">📦</span>
                  </div>

                  <div class="flex-1 min-w-0">
                    <h6 class="font-semibold truncate" :class="getRarityColor(item.rarity)">
                      {{ item.name }}
                    </h6>
                    <p class="text-sm text-base-content/70 truncate">
                      {{ item.description }}
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                      <span class="text-xs px-2 py-1 rounded-full font-medium"
                            :class="[getRarityBg(item.rarity), getRarityColor(item.rarity)]">
                        {{ item.rarity.charAt(0).toUpperCase() + item.rarity.slice(1) }}
                      </span>
                      <span class="text-xs text-base-content/60">
                        {{ item.type }}
                      </span>
                    </div>
                  </div>

                  <div class="text-right">
                    <div class="text-lg font-bold text-accent">
                      ×{{ item.quantity }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-base-300/30">
            <Button variant="primary" size="lg" class="w-full" @click="closeRewardsModal">
              Parfait ! 🎊
            </Button>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>

<style scoped>
input[type="text"] {
  text-transform: uppercase;
}
</style>