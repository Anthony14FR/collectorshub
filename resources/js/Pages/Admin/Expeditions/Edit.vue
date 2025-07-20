<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

interface ExpeditionReward {
  type: string;
  amount?: number;
  item_id?: number;
  quantity?: number;
  item?: {
    id: number;
    name: string;
    type: string;
    rarity: string;
  };
}

interface ExpeditionRequirement {
  id: number;
  type: string;
  value: string;
  quantity: number;
}

interface Expedition {
  id: number;
  name: string;
  description: string;
  rarity: string;
  duration_minutes: number;
  rewards: ExpeditionReward[];
  is_active: boolean;
  requirements: ExpeditionRequirement[];
  created_at: string;
  updated_at: string;
}

interface Item {
  id: number;
  name: string;
  type: string;
  rarity: string;
}

interface Props {
  expedition: Expedition;
  rarities: string[];
  types: string[];
  items: Item[];
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
  name: props.expedition.name,
  description: props.expedition.description,
  rarity: props.expedition.rarity,
  duration_minutes: props.expedition.duration_minutes,
  is_active: props.expedition.is_active,
  rewards: [...props.expedition.rewards],
  requirements: [...props.expedition.requirements]
});

const isSubmitting = ref(false);
const showDeleteModal = ref(false);
const selectedRewards = ref<ExpeditionReward[]>([...props.expedition.rewards]);
const selectedRequirements = ref<ExpeditionRequirement[]>([...props.expedition.requirements]);

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
      id: Date.now(),
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

const submit = () => {
  isSubmitting.value = true;
  form.put(`/admin/expeditions/${props.expedition.id}`, {
    onSuccess: () => {
      router.visit('/admin/expeditions');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const deleteExpedition = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(`/admin/expeditions/${props.expedition.id}`, {
    onSuccess: () => router.visit('/admin/expeditions'),
    onFinish: () => {
      showDeleteModal.value = false;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
};

const goBack = () => {
  router.visit('/admin/expeditions');
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

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatDuration = (minutes: number) => {
  if (minutes < 60) {
    return `${minutes} minute${minutes > 1 ? 's' : ''}`;
  }
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}min` : `${hours} heure${hours > 1 ? 's' : ''}`;
};

onMounted(() => {
  const setupRequirementOptions = () => {
    const requirementTypeSelect = document.getElementById('requirement-type') as HTMLSelectElement;
    const requirementValueSelect = document.getElementById('requirement-value') as HTMLSelectElement;

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
          props.types.forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            requirementValueSelect.appendChild(option);
          });
        }
      });
    }
  };

  setTimeout(setupRequirementOptions, 100);
});
</script>

<template>
  <Head title="Modifier l'expédition" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ✏️ MODIFIER EXPÉDITION
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Édition de {{ props.expedition.name }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
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
                          {{ reward.item?.name || getItemById(reward.item_id!)?.name }} ({{ reward.quantity }})
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
                    <div v-for="(requirement, index) in selectedRequirements" :key="requirement.id" class="flex items-center justify-between p-3 bg-base-200/30 rounded-lg">
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
                    {{ isSubmitting || form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
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
                  <span class="text-xl">🗺️</span>
                  EXPÉDITION ACTUELLE
                </h3>
              </div>
              <div class="p-6">
                <div class="text-center mb-4">
                  <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-warning/20 to-warning/10 flex items-center justify-center text-2xl font-bold mb-3">
                    🗺️
                  </div>
                  <div class="font-bold text-xl text-warning">{{ props.expedition.name }}</div>
                  <div class="text-sm text-base-content/70">{{ getRarityLabel(props.expedition.rarity) }}</div>
                </div>

                <div class="space-y-3 text-sm">
                  <div class="flex justify-between">
                    <span class="text-base-content/70">ID:</span>
                    <span class="font-medium">#{{ props.expedition.id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Créé le:</span>
                    <span class="font-medium">{{ formatDate(props.expedition.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Modifié le:</span>
                    <span class="font-medium">{{ formatDate(props.expedition.updated_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Durée:</span>
                    <span class="font-medium text-info">{{ formatDuration(props.expedition.duration_minutes) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Récompenses:</span>
                    <span class="font-medium text-success">{{ props.expedition.rewards.length }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Prérequis:</span>
                    <span class="font-medium text-secondary">{{ props.expedition.requirements.length }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚠️</span>
                  ZONE DANGER
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <p class="text-sm text-base-content/70">
                  La suppression d'une expédition est définitive et supprimera toutes ses données.
                </p>
                <Button
                  @click="deleteExpedition"
                  variant="outline"
                  size="sm"
                  class="w-full border-error text-error hover:bg-error hover:text-error-content"
                >
                  🗑️ Supprimer l'expédition
                </Button>
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
                  @click="router.visit(`/admin/expeditions/${props.expedition.id}`)"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  👁️ Voir l'expédition
                </Button>
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

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  APERÇU MODIFICATIONS
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-warning">{{ selectedRewards.length }}</div>
                  <div class="text-sm text-base-content/70">Récompenses configurées</div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-info">{{ selectedRequirements.length }}</div>
                    <div class="text-xs text-base-content/70">Prérequis</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-success">{{ formatDuration(form.duration_minutes) }}</div>
                    <div class="text-xs text-base-content/70">Durée</div>
                  </div>
                </div>
                <div class="text-center pt-2 border-t border-base-300/30">
                  <div class="text-sm text-base-content/70 mb-1">Statut</div>
                  <div class="text-lg font-bold" :class="form.is_active ? 'text-success' : 'text-error'">
                    {{ form.is_active ? '🟢 Active' : '🔴 Inactive' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="cancelDelete" max-width="md">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-error/20 rounded-lg flex items-center justify-center">
            <span class="text-xl">⚠️</span>
          </div>
          <h3 class="text-xl font-bold text-base-content">Supprimer l'expédition</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir supprimer l'expédition
          <span class="font-bold text-error">{{ props.expedition.name }}</span> ?
        </p>
        <p class="text-sm text-base-content/60">
          Cette action est irréversible et supprimera toutes les données associées.
        </p>

        <div class="flex gap-3 pt-4">
          <Button @click="confirmDelete" variant="outline" class="flex-1 border-error text-error hover:bg-error hover:text-error-content">
            🗑️ Supprimer
          </Button>
          <Button @click="cancelDelete" variant="secondary" class="flex-1">
            Annuler
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>
