<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import { getRarityDotColor, getRarityLabel, getRequirementLabel, getRewardLabel } from '@/utils/expedition';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Zap, ArrowLeft, Edit, Save, Loader2, Info, RotateCcw, Home, Map, BarChart3, DollarSign, Star, Circle, Sparkles, Gift, Target, Shield, X, CheckCircle, Clock, Users, Award } from 'lucide-vue-next';

interface Expedition {
  id: number;
  name: string;
  description: string;
  rarity: string;
  duration_minutes: number;
  rewards: Array<{
    type: string;
    amount?: number;
    item_id?: number;
    quantity?: number;
  }>;
  requirements: Array<{
    type: string;
    value: string;
    quantity: number;
  }>;
  is_active: boolean;
}

interface Props {
  expedition: Expedition;
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
  name: props.expedition.name,
  description: props.expedition.description,
  rarity: props.expedition.rarity,
  duration_minutes: props.expedition.duration_minutes,
  rewards: [...props.expedition.rewards],
  requirements: [...props.expedition.requirements],
  is_active: props.expedition.is_active,
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
  { value: 'cash', label: 'Cash', icon: DollarSign },
  { value: 'xp', label: 'XP', icon: Star },
  { value: 'pokeball', label: 'Pokéball', icon: Circle },
  { value: 'masterball', label: 'Masterball', icon: Sparkles },
  { value: 'item', label: 'Item', icon: Gift }
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

  router.put(`/admin/expeditions/${props.expedition.id}`, {
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
  <Head title="Modifier une expédition" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider flex items-center gap-2">
            <component :is="Edit" :size="28" class="inline align-middle mr-2" />
            MODIFIER EXPÉDITION
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ expedition.name }}
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 xl:gap-6">

          <div class="xl:col-span-3 order-1 xl:order-1">
            <div class="space-y-4">

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Zap" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Button @click="submit" variant="primary" size="sm" class="w-full justify-start" :disabled="form.processing">
                    <component :is="form.processing ? Loader2 : Save" :size="16" class="mr-2" :class="{ 'animate-spin': form.processing }" />
                    {{ form.processing ? 'Modification...' : 'Modifier l\'expédition' }}
                  </Button>
                  <Button @click="cancel" variant="outline" size="sm" class="w-full justify-start" :disabled="form.processing">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour
                  </Button>
                  <Button @click="router.visit('/admin/')" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="Home" :size="16" class="mr-2" /> Dashboard
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Info" :size="18" />
                    AIDE
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Modifiez les paramètres de cette expédition selon vos besoins.
                  </div>
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Les changements seront appliqués immédiatement.
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Award" :size="18" />
                    APERÇU
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div v-if="form.name" class="text-xs">
                    <div class="font-medium text-base-content mb-1">Nom:</div>
                    <div class="text-base-content/70">{{ form.name }}</div>
                  </div>
                  <div v-if="form.description" class="text-xs">
                    <div class="font-medium text-base-content mb-1">Description:</div>
                    <div class="text-base-content/70">{{ form.description }}</div>
                  </div>
                  <div class="text-xs">
                    <div class="font-medium text-base-content mb-1">Durée:</div>
                    <div class="text-base-content/70">{{ formattedDuration }}</div>
                  </div>
                  <div class="text-xs">
                    <div class="font-medium text-base-content mb-1">Récompenses:</div>
                    <div class="text-base-content/70">{{ form.rewards.length }} ajoutée(s)</div>
                  </div>
                  <div class="text-xs">
                    <div class="font-medium text-base-content mb-1">Prérequis:</div>
                    <div class="text-base-content/70">{{ form.requirements.length }} ajouté(s)</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="Map" :size="18" />
                  MODIFIER EXPÉDITION
                </h3>
              </div>

              <div class="p-6">
                <form @submit.prevent="submit" class="space-y-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-base-content mb-1">Nom de l'expédition</label>
                      <input v-model="form.name" type="text"
                             class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200 placeholder:text-base-content/50"
                             :class="{ 'border-error focus:ring-error/20 focus:border-error/50': form.errors.name }"
                             placeholder="Nom de l'expédition" required />
                      <div v-if="form.errors.name" class="text-error text-sm">{{ form.errors.name }}</div>
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-base-content mb-1">Rareté</label>
                      <select v-model="form.rarity"
                              class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200"
                              :class="{ 'border-error focus:ring-error/20 focus:border-error/50': form.errors.rarity }">
                        <option v-for="rarity in rarities" :key="rarity" :value="rarity">
                          {{ getRarityLabel(rarity) }}
                        </option>
                      </select>
                      <div v-if="form.errors.rarity" class="text-error text-sm">{{ form.errors.rarity }}</div>
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-medium text-base-content mb-1">Description</label>
                    <textarea v-model="form.description"
                              class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200 placeholder:text-base-content/50 resize-none h-24"
                              :class="{ 'border-error focus:ring-error/20 focus:border-error/50': form.errors.description }"
                              placeholder="Description de l'expédition" required />
                    <div v-if="form.errors.description" class="text-error text-sm">{{ form.errors.description }}</div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-base-content mb-1 flex items-center gap-2">
                        <component :is="Clock" :size="14" />
                        Durée (minutes)
                      </label>
                      <input v-model.number="form.duration_minutes" type="number" step="0.1" min="0.1"
                             class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200 placeholder:text-base-content/50"
                             :class="{ 'border-error focus:ring-error/20 focus:border-error/50': form.errors.duration_minutes }"
                             placeholder="0.5" required />
                      <div class="text-sm text-base-content/70">
                        Durée formatée: {{ formattedDuration }}
                      </div>
                      <div v-if="form.errors.duration_minutes" class="text-error text-sm">{{ form.errors.duration_minutes }}</div>
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-medium text-base-content mb-1 flex items-center gap-2">
                        <component :is="CheckCircle" :size="14" />
                        Statut
                      </label>
                      <div class="flex items-center gap-2">
                        <input v-model="form.is_active" type="checkbox"
                               class="w-4 h-4 text-primary bg-base-100/50 border border-base-300/30 rounded focus:ring-2 focus:ring-primary/20 focus:ring-offset-0" />
                        <span class="text-sm text-base-content/70">Active</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-semibold flex items-center gap-2">
                        <component :is="Award" :size="20" />
                        Récompenses
                      </h3>
                      <div v-if="form.errors.rewards" class="text-error text-sm">{{ form.errors.rewards }}</div>
                    </div>

                    <div class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-4 space-y-4">
                      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="space-y-2">
                          <label class="block text-sm font-medium text-base-content/70">Type</label>
                          <select v-model="newReward.type"
                                  class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200">
                            <option v-for="type in rewardTypes" :key="type.value" :value="type.value">
                              {{ type.label }}
                            </option>
                          </select>
                        </div>

                        <div v-if="newReward.type === 'item'" class="space-y-2">
                          <label class="block text-sm font-medium text-base-content/70">Item</label>
                          <select v-model="newReward.item_id"
                                  class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200">
                            <option value="">Sélectionner un item</option>
                            <option v-for="item in items" :key="item.id" :value="item.id">
                              {{ item.name }}
                            </option>
                          </select>
                        </div>

                        <div v-if="newReward.type === 'item'" class="space-y-2">
                          <label class="block text-sm font-medium text-base-content/70">Quantité</label>
                          <input v-model.number="newReward.quantity" type="number" min="1"
                                 class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200 placeholder:text-base-content/50"
                                 placeholder="1" />
                        </div>

                        <div v-if="newReward.type !== 'item'" class="space-y-2">
                          <label class="block text-sm font-medium text-base-content/70">Montant</label>
                          <input v-model.number="newReward.amount" type="number" min="1"
                                 class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200 placeholder:text-base-content/50"
                                 placeholder="100" />
                        </div>

                        <Button @click="addReward" type="button" variant="primary" size="sm"
                                :disabled="newReward.type === 'item' && !newReward.item_id">
                          <component :is="Plus" :size="14" class="mr-1" />
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
                                    class="w-6 h-6 p-0 min-h-0 rounded-full flex items-center justify-center">
                              <component :is="X" :size="12" />
                            </Button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-semibold flex items-center gap-2">
                        <component :is="Shield" :size="20" />
                        Prérequis
                      </h3>
                      <div v-if="form.errors.requirements" class="text-error text-sm">{{ form.errors.requirements }}</div>
                    </div>

                    <div class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-4 space-y-4">
                      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="space-y-2">
                          <label class="block text-sm font-medium text-base-content/70">Type</label>
                          <select v-model="newRequirement.type"
                                  class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200">
                            <option v-for="type in requirementTypes" :key="type.value" :value="type.value">
                              {{ type.label }}
                            </option>
                          </select>
                        </div>

                        <div class="space-y-2">
                          <label class="block text-sm font-medium text-base-content/70">Valeur</label>
                          <select v-model="newRequirement.value"
                                  class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200">
                            <option v-for="option in availableRequirementValues" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>

                        <div class="space-y-2">
                          <label class="block text-sm font-medium text-base-content/70">Quantité</label>
                          <input v-model.number="newRequirement.quantity" type="number" min="1"
                                 class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200 placeholder:text-base-content/50"
                                 placeholder="1" />
                        </div>

                        <Button @click="addRequirement" type="button" variant="primary" size="sm">
                          <component :is="Plus" :size="14" class="mr-1" />
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
                                    class="w-6 h-6 p-0 min-h-0 rounded-full flex items-center justify-center">
                              <component :is="X" :size="12" />
                            </Button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
