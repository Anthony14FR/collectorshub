<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Alert from '@/Components/UI/Alert.vue';

interface Props {
  typeImages: Record<string, string>;
  userCash: number;
  canCreateClub: boolean;
}

const props = defineProps<Props>();

const form = ref({
  name: '',
  description: '',
  icon: ''
});

const processing = ref(false);
const selectedCategory = ref('all');

const iconCategories = computed(() => {
  const categories = ['all'];
  Object.keys(props.typeImages).forEach(typeName => {
    if (!categories.includes(typeName.toLowerCase())) {
      categories.push(typeName.toLowerCase());
    }
  });
  return categories;
});

const filteredIcons = computed(() => {
  if (selectedCategory.value === 'all') {
    return props.typeImages;
  }
  
  const filtered: Record<string, string> = {};
  Object.entries(props.typeImages).forEach(([name, path]) => {
    if (name.toLowerCase().includes(selectedCategory.value)) {
      filtered[name] = path;
    }
  });
  return filtered;
});

const canSubmit = computed(() => {
  return form.value.name.trim() && 
    form.value.description.trim() && 
    form.value.icon && 
    props.canCreateClub && 
    !processing.value;
});

const selectIcon = (imagePath: string) => {
  form.value.icon = imagePath;
};

const goBack = () => {
  router.visit('/clubs');
};

const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    icon: ''
  };
  selectedCategory.value = 'all';
};

const submit = () => {
  if (!canSubmit.value) return;
  
  processing.value = true;
  
  router.post('/clubs', form.value, {
    onFinish: () => {
      processing.value = false;
    }
  });
};

const formatPrice = (amount: number) => {
  return new Intl.NumberFormat('fr-FR').format(amount);
};
</script> 

<template>
  <Head title="Créer un Club" />
  
  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-8 px-4">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏗️ CRÉER UN CLUB
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            FONDEZ VOTRE COMMUNAUTÉ
          </p>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-4 p-4 lg:p-8">
        <div class="w-full lg:w-64 order-1 lg:order-1">
          <Button
            @click="goBack"
            variant="secondary"
            size="sm"
            class="w-full mb-4"
          >
            ← Retour aux clubs
          </Button>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
            <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">💰</span>
                COÛT
              </h3>
            </div>

            <div class="p-3">
              <Alert 
                :type="canCreateClub ? 'success' : 'warning'" 
                variant="ghost"
              >
                <template #icon>
                  <span class="text-lg">💳</span>
                </template>
                <div>
                  <div class="text-sm font-bold mb-1">100,000 Cash requis</div>
                  <div class="text-xs">
                    Vous avez {{ formatPrice(userCash) }} Cash
                  </div>
                  <div v-if="!canCreateClub" class="text-xs text-error mt-1">
                    Fonds insuffisants
                  </div>
                </div>
              </Alert>
            </div>
          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📊</span>
                PROGRESSION
              </h3>
            </div>
            
            <div class="p-3 space-y-2">
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Nom</span>
                <span :class="[
                  'text-sm font-bold',
                  form.name.trim() ? 'text-success' : 'text-base-content/50'
                ]">
                  {{ form.name.trim() ? '✓' : '○' }}
                </span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Description</span>
                <span :class="[
                  'text-sm font-bold',
                  form.description.trim() ? 'text-success' : 'text-base-content/50'
                ]">
                  {{ form.description.trim() ? '✓' : '○' }}
                </span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Icône</span>
                <span :class="[
                  'text-sm font-bold',
                  form.icon ? 'text-success' : 'text-base-content/50'
                ]">
                  {{ form.icon ? '✓' : '○' }}
                </span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Fonds</span>
                <span :class="[
                  'text-sm font-bold',
                  canCreateClub ? 'text-success' : 'text-error'
                ]">
                  {{ canCreateClub ? '✓' : '✗' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex-1 order-2 lg:order-2">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📝</span>
                INFORMATIONS DU CLUB
              </h3>
            </div>

            <form @submit.prevent="submit" class="p-4 space-y-6">
              <div>
                <label for="name" class="block text-sm font-medium text-base-content/80 mb-2">
                  Nom du Club *
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="w-full bg-base-200/50 border border-base-300/30 rounded-lg px-4 py-3 text-base-content focus:border-warning/50 focus:outline-none focus:ring-2 focus:ring-warning/20 transition-all duration-300"
                  placeholder="Entrez le nom de votre club"
                  maxlength="50"
                  required
                />
                <div class="flex justify-between mt-1">
                  <p class="text-xs text-base-content/60">Maximum 50 caractères</p>
                  <p class="text-xs text-base-content/60">{{ form.name.length }}/50</p>
                </div>
              </div>

              <div>
                <label for="description" class="block text-sm font-medium text-base-content/80 mb-2">
                  Description *
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  class="w-full bg-base-200/50 border border-base-300/30 rounded-lg px-4 py-3 text-base-content focus:border-warning/50 focus:outline-none focus:ring-2 focus:ring-warning/20 transition-all duration-300 resize-none"
                  placeholder="Décrivez votre club et ses objectifs"
                  maxlength="500"
                  required
                ></textarea>
                <div class="flex justify-between mt-1">
                  <p class="text-xs text-base-content/60">Maximum 500 caractères</p>
                  <p class="text-xs text-base-content/60">{{ form.description.length }}/500</p>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-base-content/80 mb-2">
                  Icône du Club *
                </label>

                <div class="bg-base-200/30 rounded-xl p-4 max-h-80 overflow-y-auto">
                  <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    <div
                      v-for="(imagePath, typeName) in filteredIcons"
                      :key="typeName"
                      @click="selectIcon(imagePath)"
                      class="relative cursor-pointer group"
                    >
                      <div 
                        :class="[
                          'w-full aspect-square bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-xl border-2 transition-all duration-300 hover:scale-105 flex items-center justify-center p-2',
                          form.icon === imagePath 
                            ? 'border-warning/60 bg-warning/10 shadow-lg shadow-warning/20' 
                            : 'border-base-300/30 hover:border-warning/40'
                        ]"
                      >
                        <img 
                          :src="imagePath" 
                          :alt="typeName"
                          class="w-full h-full object-contain rounded-lg"
                        />
                      </div>
                      
                      <div 
                        v-if="form.icon === imagePath"
                        class="absolute -top-1 -right-1 w-6 h-6 bg-warning rounded-full flex items-center justify-center shadow-lg"
                      >
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                      </div>
                      
                      <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="p-1 text-center">
                          <span class="text-xs text-white font-medium">{{ typeName }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <p class="text-xs text-base-content/60 mt-2">
                  Cliquez sur une icône pour la sélectionner
                </p>
              </div>

              <div class="flex gap-4 pt-4 border-t border-base-300/30">
                <Button
                  type="button"
                  @click="resetForm"
                  variant="ghost"
                  size="md"
                  class="flex-1"
                >
                  🔄 Réinitialiser
                </Button>
                <Button
                  type="submit"
                  variant="primary"
                  size="md"
                  class="flex-1"
                  :disabled="!canSubmit"
                >
                  <span v-if="processing" class="flex items-center gap-2">
                    <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    Création...
                  </span>
                  <span v-else class="flex items-center gap-2">
                    <span class="text-lg">🏗️</span>
                    Créer le Club
                  </span>
                </Button>
              </div>
            </form>
          </div>
        </div>

        <div class="w-full lg:w-64 order-3 lg:order-3">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
            <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">👀</span>
                APERÇU
              </h3>
            </div>

            <div class="p-4">
              <div class="bg-base-200/30 rounded-xl p-4 border border-base-300/20">
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-12 h-12 bg-gradient-to-br from-base-300/20 to-base-300/10 rounded-lg flex items-center justify-center border border-base-300/30">
                    <img 
                      v-if="form.icon" 
                      :src="form.icon" 
                      alt="Icône du club"
                      class="w-8 h-8 object-contain"
                    />
                    <span v-else class="text-lg text-base-content/40">🏆</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-sm truncate">
                      {{ form.name || 'Nom du club' }}
                    </h4>
                    <p class="text-xs text-base-content/60">0/30 membres</p>
                  </div>
                </div>
                
                <p class="text-xs text-base-content/70 mb-3 h-12 overflow-hidden">
                  {{ form.description || 'Description du club...' }}
                </p>
                
                <div class="flex gap-2 text-xs">
                  <span class="bg-success/10 text-success px-2 py-1 rounded-full">0 CP</span>
                  <span class="bg-info/10 text-info px-2 py-1 rounded-full">Nouveau</span>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">💡</span>
                CONSEILS
              </h3>
            </div>
            
            <div class="p-3 space-y-2 text-xs text-base-content/70">
              <p>• <strong>Nom unique :</strong> Choisissez un nom mémorable</p>
              <p>• <strong>Description claire :</strong> Expliquez vos objectifs</p>
              <p>• <strong>Icône appropriée :</strong> Représentez votre style</p>
              <p>• <strong>Coût unique :</strong> 100,000 Cash pour créer</p>
              <p>• <strong>Limite :</strong> Maximum 30 membres par club</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>