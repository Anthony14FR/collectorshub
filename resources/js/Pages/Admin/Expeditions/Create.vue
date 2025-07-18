<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import { getRarityDotColor, getRarityLabel, getRequirementLabel, getRewardLabel } from '@/utils/expedition';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Props {
  items: Array<{
    id: number;
    name: string;
    image?: string;
  }>;
  availableTypes: string[];
  rarities: string[];
}

const props = defineProps<Props>();

const form = ref({
  name: '',
  description: '',
  rarity: 'normal',
  duration_minutes: 0.5,
  rewards: [] as Array<{
    type: string;
    amount?: number;
    item_id?: number;
    quantity?: number;
  }>,
  requirements: [] as Array<{
    type: string;
    value: string;
    quantity: number;
  }>,
  is_active: true,
  processing: false,
  errors: {} as Record<string, string>
});

const newReward = ref({
  type: 'cash',
  amount: 100,
  item_id: undefined as number | undefined,
  quantity: 1
});

const newRequirement = ref({
  type: 'rarity',
  value: 'normal',
  quantity: 1
});

const rewardTypes = [
  { value: 'cash', label: 'Cash', icon: '$' },
  { value: 'xp', label: 'XP', icon: 'XP' },
  { value: 'pokeball', label: 'Pokéball', icon: '⚾' },
  { value: 'masterball', label: 'Masterball', icon: '🏀' },
  { value: 'item', label: 'Item', icon: '🎁' }
];

const requirementTypes = [
  { value: 'rarity', label: 'Rareté' },
  { value: 'type', label: 'Type' }
];

const formattedDuration = computed(() => {
  const minutes = form.value.duration_minutes;
  if (minutes < 1) {
    const seconds = Math.round(minutes * 60);
    return seconds + 's';
  } else if (minutes < 60) {
    const wholeMinutes = Math.floor(minutes);
    const remainingSeconds = Math.round((minutes - wholeMinutes) * 60);
    if (remainingSeconds === 0) {
      return wholeMinutes + 'min';
    }
    return wholeMinutes + 'min ' + remainingSeconds + 's';
  } else {
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = Math.floor(minutes % 60);

    let result = hours + 'h';
    if (remainingMinutes > 0) {
      result += ' ' + remainingMinutes + 'min';
    }
    return result;
  }
});

const mergeRewards = (rewards: Array<any>) => {
  const merged: Array<any> = [];

  rewards.forEach(reward => {
    const existingIndex = merged.findIndex(r =>
      r.type === reward.type &&
      (reward.type !== 'item' || r.item_id === reward.item_id)
    );

    if (existingIndex >= 0) {
      if (reward.type === 'item') {
        merged[existingIndex].quantity += reward.quantity;
      } else {
        merged[existingIndex].amount += reward.amount;
      }
    } else {
      merged.push({ ...reward });
    }
  });

  return merged;
};

const mergeRequirements = (requirements: Array<any>) => {
  const merged: Array<any> = [];

  requirements.forEach(req => {
    const existingIndex = merged.findIndex(r =>
      r.type === req.type && r.value === req.value
    );

    if (existingIndex >= 0) {
      merged[existingIndex].quantity += req.quantity;
    } else {
      merged.push({ ...req });
    }
  });

  return merged;
};

const addReward = () => {
  const newRewardData = {
    type: newReward.value.type,
    ...(newReward.value.type === 'item'
      ? { item_id: newReward.value.item_id, quantity: newReward.value.quantity }
      : { amount: newReward.value.amount })
  };

  form.value.rewards.push(newRewardData);
  form.value.rewards = mergeRewards(form.value.rewards);

  newReward.value = {
    type: 'cash',
    amount: 100,
    item_id: undefined,
    quantity: 1
  };
};

const removeReward = (index: number) => {
  form.value.rewards.splice(index, 1);
};

const addRequirement = () => {
  const newReqData = {
    type: newRequirement.value.type,
    value: newRequirement.value.value,
    quantity: newRequirement.value.quantity
  };

  form.value.requirements.push(newReqData);
  form.value.requirements = mergeRequirements(form.value.requirements);

  newRequirement.value = {
    type: 'rarity',
    value: 'normal',
    quantity: 1
  };
};

const removeRequirement = (index: number) => {
  form.value.requirements.splice(index, 1);
};

const submit = () => {
  form.value.processing = true;
  form.value.errors = {};

  router.post('/admin/expeditions', {
    name: form.value.name,
    description: form.value.description,
    rarity: form.value.rarity,
    duration_minutes: form.value.duration_minutes,
    rewards: form.value.rewards,
    requirements: form.value.requirements,
    is_active: form.value.is_active
  }, {
    preserveScroll: true,
    onSuccess: () => {
      form.value.processing = false;
      router.visit('/admin/expeditions');
    },
    onError: (errors: Record<string, string>) => {
      form.value.errors = errors;
      form.value.processing = false;
    },
    onFinish: () => {
      form.value.processing = false;
    }
  });
};

const cancel = () => {
  router.visit('/admin/expeditions');
};

const availableRequirementValues = computed(() => {
  if (newRequirement.value.type === 'rarity') {
    return props.rarities.map(r => ({ value: r, label: getRarityLabel(r) }));
  } else {
    return props.availableTypes.map(t => ({ value: t, label: t }));
  }
});

watch(() => newRequirement.value.type, (newType) => {
  if (newType === 'rarity') {
    newRequirement.value.value = 'normal';
  } else {
    newRequirement.value.value = props.availableTypes[0] || 'Normal';
  }
});
</script>

<template>

  <Head title="Créer une expédition" />

  <div class="h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative overflow-hidden">
    <BackgroundEffects />

    <div class="relative z-10 h-full w-full flex flex-col">
      <div class="flex justify-center pt-4 mb-4 flex-shrink-0">
        <div class="text-center">
          <h1
            class="text-xl md:text-2xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent mb-1 tracking-wider flex items-center gap-2">
            <svg class="w-6 h-6 md:w-8 md:h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
              </path>
            </svg>
            CRÉER EXPÉDITION
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Nouvelle expédition
          </p>
        </div>
      </div>

      <div class="flex-1 px-2 md:px-4 lg:px-8 pb-4 overflow-hidden">
        <div class="h-full max-w-4xl mx-auto w-full">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-6 h-full overflow-y-auto">
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="label">
                    <span class="label-text font-medium">Nom de l'expédition</span>
                  </label>
                  <input v-model="form.name" type="text" class="input input-bordered w-full bg-base-100/80"
                         :class="{ 'input-error': form.errors.name }" placeholder="Nom de l'expédition" required />
                  <div v-if="form.errors.name" class="text-error text-sm">{{ form.errors.name }}</div>
                </div>

                <div class="space-y-2">
                  <label class="label">
                    <span class="label-text font-medium">Rareté</span>
                  </label>
                  <select v-model="form.rarity" class="select select-bordered w-full bg-base-100/80"
                          :class="{ 'select-error': form.errors.rarity }">
                    <option v-for="rarity in rarities" :key="rarity" :value="rarity">
                      {{ getRarityLabel(rarity) }}
                    </option>
                  </select>
                  <div v-if="form.errors.rarity" class="text-error text-sm">{{ form.errors.rarity }}</div>
                </div>
              </div>

              <div class="space-y-2">
                <label class="label">
                  <span class="label-text font-medium">Description</span>
                </label>
                <textarea v-model="form.description" class="textarea textarea-bordered w-full bg-base-100/80 h-24"
                          :class="{ 'textarea-error': form.errors.description }" placeholder="Description de l'expédition"
                          required />
                <div v-if="form.errors.description" class="text-error text-sm">{{ form.errors.description }}</div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="label">
                    <span class="label-text font-medium">Durée (minutes)</span>
                  </label>
                  <input v-model.number="form.duration_minutes" type="number" step="0.1" min="0.1"
                         class="input input-bordered w-full bg-base-100/80"
                         :class="{ 'input-error': form.errors.duration_minutes }" placeholder="0.5" required />
                  <div class="text-sm text-base-content/70">
                    Durée formatée: {{ formattedDuration }}
                  </div>
                  <div v-if="form.errors.duration_minutes" class="text-error text-sm">{{ form.errors.duration_minutes }}
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="label">
                    <span class="label-text font-medium">Statut</span>
                  </label>
                  <div class="form-control">
                    <label class="label cursor-pointer">
                      <span class="label-text">Active</span>
                      <input v-model="form.is_active" type="checkbox" class="toggle toggle-primary" />
                    </label>
                  </div>
                </div>
              </div>

              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold">Récompenses</h3>
                  <div v-if="form.errors.rewards" class="text-error text-sm">{{ form.errors.rewards }}</div>
                </div>

                <div class="bg-base-200/50 rounded-lg p-4 space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="space-y-2">
                      <label class="label">
                        <span class="label-text font-medium">Type</span>
                      </label>
                      <select v-model="newReward.type" class="select select-bordered select-sm w-full bg-base-100/80">
                        <option v-for="type in rewardTypes" :key="type.value" :value="type.value">
                          {{ type.label }}
                        </option>
                      </select>
                    </div>

                    <div v-if="newReward.type === 'item'" class="space-y-2">
                      <label class="label">
                        <span class="label-text font-medium">Item</span>
                      </label>
                      <select v-model="newReward.item_id"
                              class="select select-bordered select-sm w-full bg-base-100/80">
                        <option value="">Sélectionner un item</option>
                        <option v-for="item in items" :key="item.id" :value="item.id">
                          {{ item.name }}
                        </option>
                      </select>
                    </div>

                    <div v-if="newReward.type === 'item'" class="space-y-2">
                      <label class="label">
                        <span class="label-text font-medium">Quantité</span>
                      </label>
                      <input v-model.number="newReward.quantity" type="number" min="1"
                             class="input input-bordered input-sm w-full bg-base-100/80" placeholder="1" />
                    </div>

                    <div v-if="newReward.type !== 'item'" class="space-y-2">
                      <label class="label">
                        <span class="label-text font-medium">Montant</span>
                      </label>
                      <input v-model.number="newReward.amount" type="number" min="1"
                             class="input input-bordered input-sm w-full bg-base-100/80" placeholder="100" />
                    </div>

                    <Button @click="addReward" type="button" variant="primary" size="sm"
                            :disabled="newReward.type === 'item' && !newReward.item_id">
                      Ajouter
                    </Button>
                  </div>

                  <div v-if="form.rewards.length > 0" class="space-y-2">
                    <h4 class="font-medium text-sm">Récompenses ajoutées:</h4>
                    <div class="flex flex-wrap gap-2">
                      <div v-for="(reward, index) in form.rewards" :key="index"
                           class="flex items-center gap-2 bg-primary/10 text-primary px-3 py-1 rounded-full text-sm">
                        <span>{{ getRewardLabel(reward, items) }}</span>
                        <Button @click="removeReward(index)" type="button" variant="outline" size="sm"
                                class="w-8 h-8 p-0 min-h-0 rounded-full flex items-center justify-center">
                          ×
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold">Prérequis</h3>
                  <div v-if="form.errors.requirements" class="text-error text-sm">{{ form.errors.requirements }}</div>
                </div>

                <div class="bg-base-200/50 rounded-lg p-4 space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="space-y-2">
                      <label class="label">
                        <span class="label-text font-medium">Type</span>
                      </label>
                      <select v-model="newRequirement.type"
                              class="select select-bordered select-sm w-full bg-base-100/80">
                        <option v-for="type in requirementTypes" :key="type.value" :value="type.value">
                          {{ type.label }}
                        </option>
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="label">
                        <span class="label-text font-medium">Valeur</span>
                      </label>
                      <select v-model="newRequirement.value"
                              class="select select-bordered select-sm w-full bg-base-100/80">
                        <option v-for="option in availableRequirementValues" :key="option.value" :value="option.value">
                          {{ option.label }}
                        </option>
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="label">
                        <span class="label-text font-medium">Quantité</span>
                      </label>
                      <input v-model.number="newRequirement.quantity" type="number" min="1"
                             class="input input-bordered input-sm w-full bg-base-100/80" placeholder="1" />
                    </div>

                    <Button @click="addRequirement" type="button" variant="primary" size="sm">
                      Ajouter
                    </Button>
                  </div>

                  <div v-if="form.requirements.length > 0" class="space-y-2">
                    <h4 class="font-medium text-sm">Prérequis ajoutés:</h4>
                    <div class="flex flex-wrap gap-2">
                      <div v-for="(req, index) in form.requirements" :key="index"
                           class="flex items-center gap-2 bg-warning/10 text-warning px-3 py-1 rounded-full text-sm">
                        <div v-if="req.type === 'rarity'" class="w-2 h-2 rounded-full"
                             :class="getRarityDotColor(req.value)"></div>
                        <img v-else-if="req.type === 'type'" :src="`/images/types/${req.value}.png`" :alt="req.value"
                             class="w-4 h-4 object-contain">
                        <span>{{ getRequirementLabel(req) }}</span>
                        <Button @click="removeRequirement(index)" type="button" variant="outline" size="sm"
                                class="w-8 h-8 p-0 min-h-0 rounded-full flex items-center justify-center">
                          ×
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end gap-4 pt-6 border-t border-base-300/30">
                <Button @click="cancel" type="button" variant="outline" size="md" :disabled="form.processing">
                  Annuler
                </Button>
                <Button type="submit" variant="primary" size="md" :disabled="form.processing">
                  <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2"></span>
                  Créer l'expédition
                </Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>