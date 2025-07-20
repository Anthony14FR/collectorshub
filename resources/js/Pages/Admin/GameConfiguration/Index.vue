<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Modal from '@/Components/UI/Modal.vue';
import ConfigPreview from './ConfigPreview.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface GameConfig {
  id: number;
  category: string;
  key: string;
  value: any;
  description: string;
  updated_at: string;
}

interface Props {
  configurations: Record<string, GameConfig[]>;
  categories: Record<string, string>;
}

const props = defineProps<Props>();

const expandedCategories = ref<string[]>(['level_rewards']);
const searchTerm = ref('');
const showResetModal = ref(false);
const categoryToReset = ref('');

const filteredConfigurations = computed(() => {
  if (!searchTerm.value) return props.configurations;

  const filtered: Record<string, GameConfig[]> = {};
  Object.entries(props.configurations).forEach(([category, configs]) => {
    const matchingConfigs = configs.filter(config =>
      config.key.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      config.description.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
    if (matchingConfigs.length > 0) {
      filtered[category] = matchingConfigs;
    }
  });
  return filtered;
});

const totalConfigs = computed(() => {
  return Object.values(props.configurations).reduce((total, configs) => total + configs.length, 0);
});

const modifiedConfigs = computed(() => {
  const oneWeekAgo = new Date();
  oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);

  return Object.values(props.configurations).reduce((total, configs) => {
    return total + configs.filter(config => new Date(config.updated_at) > oneWeekAgo).length;
  }, 0);
});

const toggleCategory = (category: string) => {
  const index = expandedCategories.value.indexOf(category);
  if (index > -1) {
    expandedCategories.value.splice(index, 1);
  } else {
    expandedCategories.value.push(category);
  }
};

const expandAll = () => {
  expandedCategories.value = Object.keys(props.configurations);
};

const collapseAll = () => {
  expandedCategories.value = [];
};

const editConfig = (config: GameConfig) => {
  router.visit(`/admin/game-configuration/${config.id}/edit`);
};

const resetCategory = (category: string) => {
  categoryToReset.value = category;
  showResetModal.value = true;
};

const confirmReset = () => {
  router.post(`/admin/game-configuration/reset-category`, {
    category: categoryToReset.value
  }, {
    preserveScroll: true,
    onFinish: () => {
      showResetModal.value = false;
      categoryToReset.value = '';
    }
  });
};

const cancelReset = () => {
  showResetModal.value = false;
  categoryToReset.value = '';
};

const getCategoryIcon = (category: string) => {
  const icons: Record<string, string> = {
    'level_rewards': '🏆',
    'rarity_probabilities': '🎲',
    'xp_rewards': '⭐',
    'shiny_rate': '✨',
    'catch_rates': '⚡',
    'shop_prices': '💰'
  };
  return icons[category] || '⚙️';
};

const getCategoryColor = (category: string) => {
  const colors: Record<string, string> = {
    'level_rewards': 'from-success/10 to-success/5 border-success/20',
    'rarity_probabilities': 'from-info/10 to-info/5 border-info/20',
    'xp_rewards': 'from-warning/10 to-warning/5 border-warning/20',
    'shiny_rate': 'from-error/10 to-error/5 border-error/20',
    'catch_rates': 'from-primary/10 to-primary/5 border-primary/20',
    'shop_prices': 'from-accent/10 to-accent/5 border-accent/20'
  };
  return colors[category] || 'from-secondary/10 to-secondary/5 border-secondary/20';
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <Head title="Configuration du jeu" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-secondary to-secondary/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ⚙️ CONFIGURATION DU JEU
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            {{ totalConfigs }} configuration{{ totalConfigs > 1 ? 's' : '' }} répartie{{ totalConfigs > 1 ? 's' : '' }} en {{ Object.keys(props.configurations).length }} catégorie{{ Object.keys(props.configurations).length > 1 ? 's' : '' }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-9 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                  <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                    <span class="text-2xl">📋</span>
                    CONFIGURATIONS DISPONIBLES
                  </h3>

                  <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <Input
                      v-model="searchTerm"
                      placeholder="🔍 Rechercher une config..."
                      class="w-full sm:w-64"
                      size="sm"
                    />

                    <div class="flex gap-2">
                      <Button @click="expandAll" variant="outline" size="sm">
                        📖 Tout ouvrir
                      </Button>
                      <Button @click="collapseAll" variant="outline" size="sm">
                        📕 Tout fermer
                      </Button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="p-6 space-y-4">
                <div v-for="(configs, category) in filteredConfigurations" :key="category" class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 overflow-hidden">
                  <div :class="['p-4 bg-gradient-to-r border-b', getCategoryColor(category)]">
                    <div class="flex items-center justify-between">
                      <button
                        @click="toggleCategory(category)"
                        class="flex items-center gap-3 transition-all duration-200 w-full text-left p-2 rounded-lg cursor-pointer group hover:bg-base-100/20"
                      >
                        <span class="text-2xl">{{ getCategoryIcon(category) }}</span>
                        <div class="flex-1">
                          <h4 class="text-lg font-bold tracking-wider">{{ props.categories[category] }}</h4>
                          <p class="text-sm text-base-content/70">{{ configs.length }} configuration{{ configs.length > 1 ? 's' : '' }}</p>
                        </div>
                        <span class="text-lg transition-all duration-300 ease-in-out" :class="expandedCategories.includes(category) ? 'rotate-180 text-secondary' : 'text-base-content/50'">
                          ▼
                        </span>
                      </button>
                      <Button @click="resetCategory(category)" variant="outline" size="sm" class="ml-4">
                        🔄 Reset
                      </Button>
                    </div>
                  </div>

                  <div
                    v-show="expandedCategories.includes(category)"
                    class="p-4 space-y-4"
                  >
                    <div v-for="config in configs" :key="config.id" class="bg-base-100/50 rounded-xl p-4 border border-base-300/20 hover:border-secondary/30 transition-all duration-200">
                      <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                          <div class="flex items-center gap-3 mb-2">
                            <h5 class="text-lg font-bold text-base-content">{{ config.key }}</h5>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-secondary/20 text-secondary border border-secondary/30">
                              {{ category }}
                            </span>
                          </div>
                          <p class="text-sm text-base-content/70 mb-2">{{ config.description }}</p>
                          <p class="text-xs text-base-content/50">Modifié le {{ formatDate(config.updated_at) }}</p>
                        </div>
                        <div class="flex gap-2">
                          <Button @click="editConfig(config)" variant="secondary" size="sm">
                            ✏️ Modifier
                          </Button>
                        </div>
                      </div>

                      <div class="bg-base-300/30 rounded-lg p-3 border border-base-300/20">
                        <ConfigPreview :config="config" />
                      </div>
                    </div>
                  </div>

                  <div v-if="!expandedCategories.includes(category)" class="p-6 text-center">
                    <div class="text-sm text-base-content/50 italic flex items-center justify-center gap-2">
                      <span>👆</span>
                      Cliquez pour voir {{ configs.length }} configuration{{ configs.length > 1 ? 's' : '' }}
                    </div>
                  </div>
                </div>

                <div v-if="Object.keys(filteredConfigurations).length === 0" class="text-center py-12">
                  <div class="text-6xl mb-4">🔍</div>
                  <h3 class="text-xl font-bold text-base-content mb-2">Aucune configuration trouvée</h3>
                  <p class="text-base-content/70">Essayez de modifier votre recherche</p>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-3 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit('/admin/')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  ← Dashboard
                </Button>
                <Button
                  @click="router.visit('/me')"
                  variant="ghost"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Profil
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  STATISTIQUES
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-secondary">{{ totalConfigs }}</div>
                  <div class="text-sm text-base-content/70">Total configurations</div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-success">{{ Object.keys(props.configurations).length }}</div>
                    <div class="text-xs text-base-content/70">Catégories</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-warning">{{ modifiedConfigs }}</div>
                    <div class="text-xs text-base-content/70">Modifiées</div>
                  </div>
                </div>
                <div class="space-y-2 pt-2 border-t border-base-300/30">
                  <div class="text-xs text-base-content/70 mb-2">Par catégorie:</div>
                  <div v-for="(configs, category) in props.configurations" :key="category" class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                      <span class="text-sm">{{ getCategoryIcon(category) }}</span>
                      <span class="text-xs text-base-content/70 truncate">{{ props.categories[category] }}</span>
                    </div>
                    <span class="text-sm font-bold">{{ configs.length }}</span>
                  </div>
                </div>
              </div>
            </div>

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
                    <h4 class="font-semibold text-base-content mb-1">Catégories :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• <span class="text-success font-medium">Récompenses</span> : Niveaux, XP</li>
                      <li>• <span class="text-info font-medium">Probabilités</span> : Rareté, Shiny</li>
                      <li>• <span class="text-warning font-medium">Économie</span> : Prix, Cash</li>
                      <li>• <span class="text-error font-medium">Gameplay</span> : Captures, Taux</li>
                    </ul>
                  </div>

                  <div class="pt-3 border-t border-base-300/30">
                    <p class="text-xs text-base-content/60">
                      ⚠️ Testez toujours les modifications avant la production
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showResetModal" @close="cancelReset" max-width="md">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-warning/20 rounded-lg flex items-center justify-center">
            <span class="text-xl">⚠️</span>
          </div>
          <h3 class="text-xl font-bold text-base-content">Réinitialiser la catégorie</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir réinitialiser toutes les configurations de la catégorie
          <span class="font-bold text-warning">{{ props.categories[categoryToReset] }}</span> ?
        </p>
        <p class="text-sm text-base-content/60">
          Cette action restaurera les valeurs par défaut et ne peut pas être annulée.
        </p>

        <div class="flex gap-3 pt-4">
          <Button @click="confirmReset" variant="outline" class="flex-1 border-warning text-warning hover:bg-warning hover:text-warning-content">
            🔄 Réinitialiser
          </Button>
          <Button @click="cancelReset" variant="secondary" class="flex-1">
            Annuler
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>
