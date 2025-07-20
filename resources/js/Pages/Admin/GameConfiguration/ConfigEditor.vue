<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import ConfigPreview from './ConfigPreview.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

interface GameConfig {
  id: number;
  category: string;
  key: string;
  value: any;
  description: string;
  created_at: string;
  updated_at: string;
}

interface Props {
  config: GameConfig;
  categories: Record<string, string>;
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
  category: props.config.category,
  key: props.config.key,
  value: JSON.stringify(props.config.value, null, 2),
  description: props.config.description
});

const isSubmitting = ref(false);
const jsonError = ref('');
const previewConfig = ref(props.config);

const categoryOptions = computed(() => {
  return Object.entries(props.categories).map(([value, label]) => ({
    value,
    label
  }));
});

const isValidJson = computed(() => {
  try {
    JSON.parse(form.value);
    return true;
  } catch {
    return false;
  }
});

watch(() => form.value, (newValue) => {
  jsonError.value = '';
  try {
    const parsed = JSON.parse(newValue);
    previewConfig.value = {
      ...props.config,
      value: parsed
    };
  } catch (e) {
    jsonError.value = 'JSON invalide';
  }
}, { immediate: true });

const submit = () => {
  if (!isValidJson.value) {
    jsonError.value = 'Le JSON doit être valide avant de sauvegarder';
    return;
  }

  isSubmitting.value = true;

  const formData = {
    category: form.category,
    key: form.key,
    value: JSON.parse(form.value),
    description: form.description
  };

  router.put(`/admin/game-configuration/${props.config.id}`, formData, {
    onSuccess: () => {
      router.visit('/admin/game-configuration');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const goBack = () => {
  router.visit('/admin/game-configuration');
};

const formatJson = () => {
  try {
    const parsed = JSON.parse(form.value);
    form.value = JSON.stringify(parsed, null, 2);
  } catch (e) {
    jsonError.value = 'Impossible de formatter: JSON invalide';
  }
};

const resetToDefault = () => {
  const defaults: Record<string, any> = {
    'level_rewards': {
      'milestones': {
        'milestone_5': { 'cash': 1500, 'pokeballs': 10, 'masterballs': 0 },
        'milestone_10': { 'cash': 3000, 'pokeballs': 0, 'masterballs': 5 },
        'regular_level': { 'cash': 2500, 'pokeballs': 2, 'masterballs': 0 }
      }
    },
    'rarity_probabilities': {
      'normal': 70,
      'rare': 27,
      'epic': 2.7,
      'legendary': 0.3
    }
  };

  const defaultValue = defaults[form.category] || {};
  form.value = JSON.stringify(defaultValue, null, 2);
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

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <Head title="Modifier la configuration" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ✏️ MODIFIER CONFIGURATION
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Édition de {{ props.config.key }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-xl font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">⚙️</span>
                  INFORMATIONS CONFIGURATION
                </h3>
              </div>

              <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Catégorie *
                    </label>
                    <Select
                      v-model="form.category"
                      :options="categoryOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.category" class="text-xs text-error mt-1">
                      {{ props.errors.category }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Clé de configuration *
                    </label>
                    <Input
                      v-model="form.key"
                      placeholder="ma_configuration"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.key" class="text-xs text-error mt-1">
                      {{ props.errors.key }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Description
                  </label>
                  <Input
                    v-model="form.description"
                    placeholder="Description de la configuration..."
                    class="w-full"
                  />
                  <p v-if="props.errors?.description" class="text-xs text-error mt-1">
                    {{ props.errors.description }}
                  </p>
                </div>

                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <label class="block text-sm font-bold text-base-content/80">
                      Valeur JSON *
                    </label>
                    <div class="flex gap-2">
                      <Button @click="formatJson" type="button" variant="outline" size="sm">
                        🎨 Formatter
                      </Button>
                      <Button @click="resetToDefault" type="button" variant="outline" size="sm">
                        🔄 Défaut
                      </Button>
                    </div>
                  </div>

                  <div class="relative">
                    <textarea
                      v-model="form.value"
                      placeholder='{"example": "value"}'
                      class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50 font-mono text-sm"
                      rows="12"
                      :class="{ 'border-error': jsonError || props.errors?.value }"
                    ></textarea>

                    <div v-if="!isValidJson" class="absolute top-2 right-2">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-error/20 text-error border border-error/30">
                        ❌ JSON invalide
                      </span>
                    </div>
                    <div v-else class="absolute top-2 right-2">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success/20 text-success border border-success/30">
                        ✅ JSON valide
                      </span>
                    </div>
                  </div>

                  <p v-if="jsonError" class="text-xs text-error mt-1">
                    {{ jsonError }}
                  </p>
                  <p v-if="props.errors?.value" class="text-xs text-error mt-1">
                    {{ props.errors.value }}
                  </p>
                  <p class="text-xs text-base-content/60">
                    Format JSON requis. Utilisez le bouton "Formatter" pour une meilleure lisibilité.
                  </p>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    👁️ Aperçu en temps réel
                  </h4>

                  <div class="bg-base-200/30 rounded-xl p-4 border border-base-300/20">
                    <ConfigPreview v-if="isValidJson" :config="previewConfig" />
                    <div v-else class="text-center py-8 text-base-content/50">
                      <div class="text-4xl mb-2">❌</div>
                      <p>Aperçu indisponible - JSON invalide</p>
                    </div>
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-base-300/30">
                  <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    :disabled="isSubmitting || form.processing || !isValidJson"
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
                  <span class="text-xl">⚙️</span>
                  CONFIG ACTUELLE
                </h3>
              </div>
              <div class="p-6">
                <div class="text-center mb-4">
                  <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-warning/20 to-warning/10 flex items-center justify-center text-2xl font-bold mb-3">
                    {{ getCategoryIcon(props.config.category) }}
                  </div>
                  <div class="font-bold text-xl text-warning">{{ props.config.key }}</div>
                  <div class="text-sm text-base-content/70">{{ props.categories[props.config.category] }}</div>
                </div>

                <div class="space-y-3 text-sm">
                  <div class="flex justify-between">
                    <span class="text-base-content/70">ID:</span>
                    <span class="font-medium">#{{ props.config.id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Créé le:</span>
                    <span class="font-medium">{{ formatDate(props.config.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Modifié le:</span>
                    <span class="font-medium">{{ formatDate(props.config.updated_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Catégorie:</span>
                    <span class="font-medium text-secondary">{{ props.config.category }}</span>
                  </div>
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
                  @click="router.visit('/admin/game-configuration')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste configurations
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
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">💡</span>
                  AIDE JSON
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-sm space-y-3">
                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Exemples par catégorie :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• <span class="text-success font-medium">Récompenses</span> : {"cash": 1000, "items": []}</li>
                      <li>• <span class="text-info font-medium">Probabilités</span> : {"normal": 70, "rare": 30}</li>
                      <li>• <span class="text-warning font-medium">Taux</span> : {"base_rate": 0.05}</li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Validation :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• JSON valide requis</li>
                      <li>• Utilisez des guillemets doubles</li>
                      <li>• Pas de virgule en fin de ligne</li>
                    </ul>
                  </div>

                  <div class="pt-3 border-t border-base-300/30">
                    <p class="text-xs text-base-content/60">
                      ⚠️ L'aperçu se met à jour automatiquement
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
