<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Item {
  id: number;
  name: string;
  type: string;
}

interface Props {
  rarities: string[];
  types: string[];
  items: Item[];
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
  name: '',
  description: '',
  rarity: 'normal',
  duration_minutes: 30,
  is_active: true,
  rewards: [] as Array<{ type: string; amount?: number; item_id?: number; quantity?: number }>,
  requirements: [] as Array<{ type: string; value: string; quantity: number }>
});

const isSubmitting = ref(false);
const selectedRewards = ref<Array<{ type: string; amount?: number; item_id?: number; quantity?: number }>>([]);
const selectedRequirements = ref<Array<{ type: string; value: string; quantity: number }>>([]);

const rarityOptions = computed(() => {
  return props.rarities.map(rarity => ({
    value: rarity,
    label: getRarityLabel(rarity)
  }));
});

const typeOptions = computed(() => {
  return props.types.map(type => ({
    value: type,
    label: type
  }));
});

const itemOptions = computed(() => {
  return props.items.map(item => ({
    value: item.id.toString(),
    label: `${item.name} (${item.type})`
  }));
});

const rewardTypeOptions = [
  { value: 'cash', label: 'Cash 💰' },
  { value: 'xp', label: 'XP ⭐' },
  { value: 'pokeball', label: 'Pokéballs ⚪' },
  { value: 'masterball', label: 'Masterballs 🟣' },
  { value: 'item', label: 'Item 🎁' }
];

const requirementTypeOptions = [
  { value: 'rarity', label: 'Rareté de Pokémon' },
  { value: 'type', label: 'Type de Pokémon' }
];

const statusOptions = [
  { value: true, label: 'Active' },
  { value: false, label: 'Inactive' }
];

const addReward = () => {
  const rewardType = (document.getElementById('reward-type') as HTMLSelectElement)?.value;
  if (!rewardType) return;

  const reward: any = { type: rewardType };

  if (rewardType === 'item') {
    const itemId = parseInt((document.getElementById('reward-item') as HTMLSelectElement)?.value);
    const quantity = parseInt((document.getElementById('reward-quantity') as HTMLInputElement)?.value) || 1;
    if (itemId) {
      reward.item_id = itemId;
      reward.quantity = quantity;
    }
  } else {
    const amount = parseInt((document.getElementById('reward-amount') as HTMLInputElement)?.value) || 1;
    reward.amount = amount;
  }

  if (!selectedRewards.value.find(r => r.type === rewardType && r.item_id === reward.item_id)) {
    selectedRewards.value.push(reward);
    form.rewards = selectedRewards.value;

    (document.getElementById('reward-type') as HTMLSelectElement).value = '';
    (document.getElementById('reward-amount') as HTMLInputElement).value = '';
    (document.getElementById('reward-item') as HTMLSelectElement).value = '';
    (document.getElementById('reward-quantity') as HTMLInputElement).value = '1';
  }
};

const removeReward = (index: number) => {
  selectedRewards.value.splice(index, 1);
  form.rewards = selectedRewards.value;
};

const addRequirement = () => {
  const requirementType = (document.getElementById('requirement-type') as HTMLSelectElement)?.value;
  const requirementValue = (document.getElementById('requirement-value') as HTMLSelectElement)?.value;
  const quantity = parseInt((document.getElementById('requirement-quantity') as HTMLInputElement)?.value) || 1;

  if (requirementType && requirementValue) {
    const requirement = {
      type: requirementType,
      value: requirementValue,
      quantity: quantity
    };

    if (!selectedRequirements.value.find(r => r.type === requirementType && r.value === requirementValue)) {
      selectedRequirements.value.push(requirement);
      form.requirements = selectedRequirements.value;

      (document.getElementById('requirement-type') as HTMLSelectElement).value = '';
      (document.getElementById('requirement-value') as HTMLSelectElement).value = '';
      (document.getElementById('requirement-quantity') as HTMLInputElement).value = '1';
    }
  }
};

const removeRequirement = (index: number) => {
  selectedRequirements.value.splice(index, 1);
  form.requirements = selectedRequirements.value;
};

const getItemById = (id: number) => {
  return props.items.find(item => item.id === id);
};

const getRarityLabel = (rarity: string) => {
  switch (rarity) {
  case 'normal': return 'Normal';
  case 'rare': return 'Rare';
  case 'epic': return 'Épique';
  case 'legendary': return 'Légendaire';
  default: return rarity;
  }
};

const getRequirementValueOptions = (requirementType: string) => {
  if (requirementType === 'rarity') {
    return props.rarities.map(rarity => ({
      value: rarity,
      label: getRarityLabel(rarity)
    }));
  } else if (requirementType === 'type') {
    return props.types.map(type => ({
      value: type,
      label: type
    }));
  }
  return [];
};

const submit = () => {
  isSubmitting.value = true;
  form.post('/admin/expeditions', {
    onSuccess: () => {
      router.visit('/admin/expeditions');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const goBack = () => {
  router.visit('/admin/expeditions');
};

const formatDuration = (minutes: number) => {
  if (minutes < 60) {
    return `${minutes} minute${minutes > 1 ? 's' : ''}`;
  }
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}min` : `${hours} heure${hours > 1 ? 's' : ''}`;
};
</script>

<template>
  <Head title="Créer une expédition" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ➕ CRÉER EXPÉDITION
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Configurer une nouvelle expédition
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
                <h3 class="text-xl font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">🗺️</span>
                  INFORMATIONS EXPÉDITION
                </h3>
              </div>

              <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Nom de l'expédition *
                    </label>
                    <Input
                      v-model="form.name"
                      placeholder="Exploration des Bois Sombres"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.name" class="text-xs text-error mt-1">
                      {{ props.errors.name }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Rareté *
                    </label>
                    <Select
                      v-model="form.rarity"
                      :options="rarityOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.rarity" class="text-xs text-error mt-1">
                      {{ props.errors.rarity }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Description *
                  </label>
                  <textarea
                    v-model="form.description"
                    placeholder="Description détaillée de l'expédition..."
                    class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50"
                    rows="3"
                    required
                  ></textarea>
                  <p v-if="props.errors?.description" class="text-xs text-error mt-1">
                    {{ props.errors.description }}
                  </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Durée (minutes) *
                    </label>
                    <Input
                      v-model="form.duration_minutes"
                      type="number"
                      min="1"
                      placeholder="30"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.duration_minutes" class="text-xs text-error mt-1">
                      {{ props.errors.duration_minutes }}
                    </p>
                    <p class="text-xs text-base-content/60">
                      {{ formatDuration(form.duration_minutes) }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Statut *
                    </label>
                    <Select
                      v-model="form.is_active"
                      :options="statusOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.is_active" class="text-xs text-error mt-1">
                      {{ props.errors.is_active }}
                    </p>
                  </div>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    🎁 Récompenses
                  </h4>

                  <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div>
                      <select id="reward-type" class="select select-bordered w-full bg-base-100/80 border-base-300/50 text-sm">
                        <option value="">Type de récompense</option>
                        <option v-for="option in rewardTypeOptions" :key="option.value" :value="option.value">
                          {{ option.label }}
                        </option>
                      </select>
                    </div>
                    <div>
                      <Input
                        id="reward-amount"
                        type="number"
                        min="1"
                        placeholder="Quantité"
                        class="w-full"
                      />
                    </div>
                    <div>
                      <select id="reward-item" class="select select-bordered w-full bg-base-100/80 border-base-300/50 text-sm">
                        <option value="">Item (si applicable)</option>
                        <option v-for="option in itemOptions" :key="option.value" :value="option.value">
                          {{ option.label }}
                        </option>
                      </select>
                    </div>
                    <div>
                      <Input
                        id="reward-quantity"
                        type="number"
                        min="1"
                        value="1"
                        placeholder="Qté item"
                        class="w-full"
                      />
                    </div>
                    <div>
                      <Button @click="addReward" type="button" variant="outline" size="sm" class="w-full">
                        Ajouter
                      </Button>
                    </div>
                  </div>

                  <div v-if="selectedRewards.length > 0" class="space-y-2">
                    <div v-for="(reward, index) in selectedRewards" :key="index" class="flex items-center justify-between p-3 bg-base-200/30 rounded-lg">
                      <div class="flex items-center gap-3">
                        <span class="font-medium">{{ rewardTypeOptions.find(r => r.value === reward.type)?.label }}</span>
                        <span v-if="reward.type !== 'item'" class="text-sm text-base-content/70">{{ reward.amount }}</span>
                        <span v-else class="text-sm text-base-content/70">
                          {{ getItemById(reward.item_id!)?.name }} ({{ reward.quantity }})
                        </span>
                      </div>
                      <Button @click="removeReward(index)" variant="ghost" size="sm" class="text-error">
                        ✕
                      </Button>
                    </div>
                  </div>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    📋 Prérequis
                  </h4>

                  <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                      <select id="requirement-type" class="select select-bordered w-full bg-base-100/80 border-base-300/50 text-sm">
                        <option value="">Type de prérequis</option>
                        <option v-for="option in requirementTypeOptions" :key="option.value" :value="option.value">
                          {{ option.label }}
                        </option>
                      </select>
                    </div>
                    <div>
                      <select id="requirement-value" class="select select-bordered w-full bg-base-100/80 border-base-300/50 text-sm">
                        <option value="">Valeur</option>
                      </select>
                    </div>
                    <div>
                      <Input
                        id="requirement-quantity"
                        type="number"
                        min="1"
                        value="1"
                        placeholder="Quantité"
                        class="w-full"
                      />
                    </div>
                    <div>
                      <Button @click="addRequirement" type="button" variant="outline" size="sm" class="w-full">
                        Ajouter
                      </Button>
                    </div>
                  </div>

                  <div v-if="selectedRequirements.length > 0" class="space-y-2">
                    <div v-for="(requirement, index) in selectedRequirements" :key="index" class="flex items-center justify-between p-3 bg-base-200/30 rounded-lg">
                      <div class="flex items-center gap-3">
                        <span class="font-medium">{{ requirementTypeOptions.find(r => r.value === requirement.type)?.label }}</span>
                        <span class="text-sm text-base-content/70">
                          {{ requirement.quantity }} × {{ requirement.type === 'rarity' ? getRarityLabel(requirement.value) : requirement.value }}
                        </span>
                      </div>
                      <Button @click="removeRequirement(index)" variant="ghost" size="sm" class="text-error">
                        ✕
                      </Button>
                    </div>
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-base-300/30">
                  <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    :disabled="isSubmitting || form.processing"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    <span v-if="isSubmitting || form.processing">⏳</span>
                    <span v-else>💾</span>
                    {{ isSubmitting || form.processing ? 'Création...' : 'Créer l\'expédition' }}
                  </Button>

                  <Button
                    @click="goBack"
                    variant="secondary"
                    size="lg"
                    :disabled="isSubmitting || form.processing"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    ← Retour à la liste
                  </Button>
                </div>
              </form>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">💡</span>
                  AIDE
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-sm space-y-3">
                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Raretés disponibles :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• <span class="text-base-content font-medium">Normal</span> : Facile, courte durée</li>
                      <li>• <span class="text-info font-medium">Rare</span> : Difficulté moyenne</li>
                      <li>• <span class="text-warning font-medium">Épique</span> : Difficile, longue durée</li>
                      <li>• <span class="text-error font-medium">Légendaire</span> : Très difficile</li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Types de récompenses :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• Cash : Monnaie du jeu</li>
                      <li>• XP : Points d'expérience</li>
                      <li>• Pokéballs/Masterballs : Objets de capture</li>
                      <li>• Items : Objets spéciaux</li>
                    </ul>
                  </div>

                  <div class="pt-3 border-t border-base-300/30">
                    <p class="text-xs text-base-content/60">
                      ⚠️ Les champs marqués d'un * sont obligatoires
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  APERÇU
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-success">{{ form.name || 'Nouvelle expédition' }}</div>
                  <div class="text-sm text-base-content/70">{{ getRarityLabel(form.rarity) }}</div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-info">{{ formatDuration(form.duration_minutes) }}</div>
                    <div class="text-xs text-base-content/70">Durée</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-warning">{{ selectedRewards.length }}</div>
                    <div class="text-xs text-base-content/70">Récompenses</div>
                  </div>
                </div>
                <div class="text-center pt-2 border-t border-base-300/30">
                  <div class="text-sm text-base-content/70 mb-1">Prérequis</div>
                  <div class="text-lg font-bold text-secondary">{{ selectedRequirements.length }}</div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🔗</span>
                  NAVIGATION
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit('/admin/expeditions')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste expéditions
                </Button>
                <Button
                  @click="router.visit('/admin')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Dashboard
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
document.addEventListener('DOMContentLoaded', function() {
  const requirementTypeSelect = document.getElementById('requirement-type');
  const requirementValueSelect = document.getElementById('requirement-value');

  if (requirementTypeSelect && requirementValueSelect) {
    requirementTypeSelect.addEventListener('change', function() {
      const selectedType = this.value;
      requirementValueSelect.innerHTML = '<option value="">Valeur</option>';

      if (selectedType === 'rarity') {
        const rarities = ['normal', 'rare', 'epic', 'legendary'];
        const labels = ['Normal', 'Rare', 'Épique', 'Légendaire'];
        rarities.forEach((rarity, index) => {
          const option = document.createElement('option');
          option.value = rarity;
          option.textContent = labels[index];
          requirementValueSelect.appendChild(option);
        });
      } else if (selectedType === 'type') {
        const types = ['Normal', 'Feu', 'Eau', 'Electrik', 'Plante', 'Glace', 'Combat', 'Poison', 'Sol', 'Vol', 'Psy', 'Insecte', 'Roche', 'Spectre', 'Dragon', 'Acier', 'Fee'];
        types.forEach(type => {
          const option = document.createElement('option');
          option.value = type;
          option.textContent = type;
          requirementValueSelect.appendChild(option);
        });
      }
    });
  }
});
</script>
