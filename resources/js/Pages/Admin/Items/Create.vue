<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Props {
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = ref({
  name: '',
  description: '',
  type: 'ball',
  price: 0,
  rarity: 'normal',
  image: null as File | null,
  effect: {}
});

const isSubmitting = ref(false);

const typeOptions = [
  { value: 'ball', label: 'Ball' },
  { value: 'avatar', label: 'Avatar' },
  { value: 'background', label: 'Background' }
];

const rarityOptions = [
  { value: 'normal', label: 'Normal' },
  { value: 'rare', label: 'Rare' },
  { value: 'epic', label: 'Épique' },
  { value: 'legendary', label: 'Légendaire' }
];

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    form.value.image = target.files[0];
  }
};

const submit = () => {
  isSubmitting.value = true;

  const formData = new FormData();
  formData.append('name', form.value.name);
  formData.append('description', form.value.description);
  formData.append('type', form.value.type);
  formData.append('price', form.value.price.toString());
  formData.append('rarity', form.value.rarity);
  formData.append('effect', JSON.stringify(form.value.effect));

  if (form.value.image) {
    formData.append('image', form.value.image);
  }

  router.post('/admin/items', formData, {
    onSuccess: () => {
      router.visit('/admin/items');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const goBack = () => {
  router.visit('/admin/items');
};

const getTypeIcon = (type: string) => {
  switch (type) {
  case 'ball': return '⚾';
  case 'avatar': return '👤';
  case 'background': return '🖼️';
  default: return '🛍️';
  }
};

const getRarityIcon = (rarity: string) => {
  switch (rarity) {
  case 'normal': return '⚪';
  case 'rare': return '🔵';
  case 'epic': return '🟣';
  case 'legendary': return '🟡';
  default: return '⚪';
  }
};
</script>

<template>
  <Head title="Créer un item" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ➕ CRÉER ITEM
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Ajouter un nouvel item à la boutique
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
                <h3 class="text-xl font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">🛍️</span>
                  INFORMATIONS ITEM
                </h3>
              </div>

              <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Nom de l'item *
                    </label>
                    <Input
                      v-model="form.name"
                      placeholder="Master Ball"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.name" class="text-xs text-error mt-1">
                      {{ props.errors.name }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Prix *
                    </label>
                    <Input
                      v-model="form.price"
                      type="number"
                      min="0"
                      placeholder="1000"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.price" class="text-xs text-error mt-1">
                      {{ props.errors.price }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Description
                  </label>
                  <textarea
                    v-model="form.description"
                    placeholder="Description de l'item..."
                    class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50"
                    rows="3"
                  ></textarea>
                  <p v-if="props.errors?.description" class="text-xs text-error mt-1">
                    {{ props.errors.description }}
                  </p>
                  <p class="text-xs text-base-content/60">
                    Description qui apparaîtra dans la boutique
                  </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Type d'item *
                    </label>
                    <Select
                      v-model="form.type"
                      :options="typeOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.type" class="text-xs text-error mt-1">
                      {{ props.errors.type }}
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
                    Image de l'item
                  </label>
                  <Input
                    @change="handleImageChange"
                    type="file"
                    accept="image/*"
                    class="w-full"
                  />
                  <p v-if="props.errors?.image" class="text-xs text-error mt-1">
                    {{ props.errors.image }}
                  </p>
                  <p class="text-xs text-base-content/60">
                    Formats acceptés: JPG, PNG, GIF, WebP (max 2MB)
                  </p>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    ⚙️ Effets spéciaux (Optionnel)
                  </h4>

                  <div class="p-4 bg-base-200/30 rounded-lg">
                    <div class="text-sm text-base-content/70 mb-2">Configuration JSON des effets</div>
                    <textarea
                      v-model="form.effect"
                      placeholder='{"catch_rate": 1.5, "special_effect": "masterball"}'
                      class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50 font-mono text-sm"
                      rows="4"
                    ></textarea>
                    <p class="text-xs text-base-content/60 mt-2">
                      Format JSON pour définir les effets spéciaux de l'item
                    </p>
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-base-300/30">
                  <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    :disabled="isSubmitting"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    <span v-if="isSubmitting">⏳</span>
                    <span v-else>💾</span>
                    {{ isSubmitting ? 'Création...' : 'Créer l\'item' }}
                  </Button>

                  <Button
                    @click="goBack"
                    variant="secondary"
                    size="lg"
                    :disabled="isSubmitting"
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
                  <span class="text-xl">👁️</span>
                  APERÇU
                </h3>
              </div>
              <div class="p-6">
                <div class="text-center">
                  <div class="w-20 h-20 mx-auto rounded-lg bg-gradient-to-br from-success/20 to-success/10 flex items-center justify-center text-3xl mb-4">
                    {{ getTypeIcon(form.type) }}
                  </div>
                  <div class="font-bold text-lg">{{ form.name || 'Nom de l\'item' }}</div>
                  <div class="text-sm text-base-content/70 mb-2">{{ form.description || 'Description...' }}</div>
                  <div class="flex justify-center gap-2 mb-3">
                    <span class="text-xs px-2 py-1 bg-info/20 text-info rounded-full">
                      {{ form.type ? typeOptions.find(t => t.value === form.type)?.label : 'Type' }}
                    </span>
                    <span class="text-xs px-2 py-1 bg-warning/20 text-warning rounded-full">
                      {{ getRarityIcon(form.rarity) }} {{ form.rarity ? rarityOptions.find(r => r.value === form.rarity)?.label : 'Rareté' }}
                    </span>
                  </div>
                  <div class="text-lg font-bold text-success">
                    {{ form.price?.toLocaleString() || '0' }} 💰
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">💡</span>
                  AIDE
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-sm space-y-3">
                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Types d'items :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• <span class="text-info font-medium">Ball</span> : Objets de capture</li>
                      <li>• <span class="text-warning font-medium">Avatar</span> : Images de profil</li>
                      <li>• <span class="text-secondary font-medium">Background</span> : Arrière-plans</li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Raretés :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• ⚪ Normal : Item commun</li>
                      <li>• 🔵 Rare : Item peu commun</li>
                      <li>• 🟣 Épique : Item très rare</li>
                      <li>• 🟡 Légendaire : Item unique</li>
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
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🔗</span>
                  NAVIGATION
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit('/admin/items')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste items
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
