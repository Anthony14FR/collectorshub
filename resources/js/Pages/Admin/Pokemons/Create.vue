<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, reactive } from 'vue';
import { getRarityColor, getTypeColor } from '@/utils/pokemon';

interface Props {
  rarities: string[];
  generations: number[];
  types: string[];
  damageRelations: string[];
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = reactive({
  pokedex_id: '',
  name: '',
  image: null as File | null,
  types: [{ name: '', image: '' }],
  resistances: [{ name: '', damage_multiplier: 1, damage_relation: 'neutral' }],
  evolution_id: '',
  pre_evolution_id: '',
  description: '',
  height: '',
  weight: '',
  rarity: 'normal',
  is_shiny: false,
  hp: '',
  attack: '',
  defense: '',
  speed: '',
  special_attack: '',
  special_defense: '',
  generation: '',
  processing: false,
  errors: {} as Record<string, string>,
});

const isSubmitting = ref(false);

const rarityOptions = computed(() => {
  return props.rarities.map(rarity => ({
    value: rarity,
    label: getRarityLabel(rarity)
  }));
});

const generationOptions = computed(() => {
  return props.generations.map(gen => ({
    value: gen.toString(),
    label: `Génération ${gen}`
  }));
});

const typeOptions = computed(() => {
  return props.types.map(type => ({
    value: type,
    label: type
  }));
});

const damageRelationOptions = [
  { value: 'resistant', label: 'Résistant' },
  { value: 'vulnerable', label: 'Vulnérable' },
  { value: 'neutral', label: 'Neutre' }
];

const shinyOptions = [
  { value: true, label: 'Shiny' },
  { value: false, label: 'Normal' }
];

const addType = () => {
  if (form.types.length < 2) {
    form.types.push({ name: '', image: '' });
  }
};

const removeType = (index: number) => {
  if (form.types.length > 1) {
    form.types.splice(index, 1);
  }
};

const addResistance = () => {
  form.resistances.push({ name: '', damage_multiplier: 1, damage_relation: 'neutral' });
};

const removeResistance = (index: number) => {
  if (form.resistances.length > 1) {
    form.resistances.splice(index, 1);
  }
};

const updateTypeImage = (index: number, typeName: string) => {
  form.types[index].image = `/images/types/${typeName}.png`;
};

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    form.image = target.files[0];
  }
};

const submit = () => {
  isSubmitting.value = true;
  form.processing = true;
  form.errors = {};

  const formData = new FormData();
  formData.append('pokedex_id', form.pokedex_id);
  formData.append('name', form.name);
  formData.append('types', JSON.stringify(form.types.filter(t => t.name)));
  formData.append('resistances', JSON.stringify(form.resistances.filter(r => r.name)));
  formData.append('evolution_id', form.evolution_id || '');
  formData.append('pre_evolution_id', form.pre_evolution_id || '');
  formData.append('description', form.description);
  formData.append('height', form.height);
  formData.append('weight', form.weight);
  formData.append('rarity', form.rarity);
  formData.append('is_shiny', form.is_shiny.toString());
  formData.append('hp', form.hp);
  formData.append('attack', form.attack);
  formData.append('defense', form.defense);
  formData.append('speed', form.speed);
  formData.append('special_attack', form.special_attack);
  formData.append('special_defense', form.special_defense);
  formData.append('generation', form.generation || '');

  if (form.image) {
    formData.append('image', form.image);
  }

  router.post('/admin/pokemons', formData, {
    onSuccess: () => {
      router.visit('/admin/pokemons');
    },
    onError: (errors: Record<string, string>) => {
      form.processing = false;
      form.errors = errors;
      isSubmitting.value = false;
    },
    onFinish: () => {
      isSubmitting.value = false;
      form.processing = false;
    },
    preserveScroll: true,
  });
};

const goBack = () => {
  router.visit('/admin/pokemons');
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
</script>

<template>
  <Head title="Créer un Pokémon" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ➕ CRÉER POKÉMON
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Ajouter un nouveau Pokémon à la base de données
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
                <h3 class="text-xl font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">⚡</span>
                  INFORMATIONS POKÉMON
                </h3>
              </div>

              <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Pokédex ID *
                    </label>
                    <Input
                      v-model="form.pokedex_id"
                      type="number"
                      placeholder="1"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.pokedex_id" class="text-xs text-error mt-1">
                      {{ props.errors.pokedex_id }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Nom du Pokémon *
                    </label>
                    <Input
                      v-model="form.name"
                      placeholder="Pikachu"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.name" class="text-xs text-error mt-1">
                      {{ props.errors.name }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Image du Pokémon
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
                    Formats acceptés: JPG, PNG, GIF (max 2MB)
                  </p>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    🎯 Types
                  </h4>

                  <div v-for="(type, index) in form.types" :key="index" class="flex gap-3 items-end">
                    <div class="flex-1">
                      <Select
                        v-model="type.name"
                        @update:model-value="updateTypeImage(index, $event)"
                        :options="typeOptions"
                        placeholder="Sélectionner un type"
                        class="w-full"
                      />
                    </div>
                    <div class="flex gap-2">
                      <Button
                        v-if="form.types.length < 2"
                        @click="addType"
                        type="button"
                        variant="outline"
                        size="sm"
                      >
                        ➕
                      </Button>
                      <Button
                        v-if="form.types.length > 1"
                        @click="removeType(index)"
                        type="button"
                        variant="outline"
                        size="sm"
                        class="text-error"
                      >
                        ✕
                      </Button>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Taille (m) *
                    </label>
                    <Input
                      v-model="form.height"
                      type="number"
                      step="0.1"
                      placeholder="0.4"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.height" class="text-xs text-error mt-1">
                      {{ props.errors.height }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Poids (kg) *
                    </label>
                    <Input
                      v-model="form.weight"
                      type="number"
                      step="0.1"
                      placeholder="6.0"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.weight" class="text-xs text-error mt-1">
                      {{ props.errors.weight }}
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Génération
                    </label>
                    <Select
                      v-model="form.generation"
                      :options="generationOptions"
                      class="w-full"
                    />
                    <p v-if="props.errors?.generation" class="text-xs text-error mt-1">
                      {{ props.errors.generation }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Type *
                    </label>
                    <Select
                      v-model="form.is_shiny"
                      :options="shinyOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.is_shiny" class="text-xs text-error mt-1">
                      {{ props.errors.is_shiny }}
                    </p>
                  </div>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    📊 Statistiques
                  </h4>

                  <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                      <label class="block text-sm font-bold text-base-content/80 mb-2">
                        PV *
                      </label>
                      <Input
                        v-model="form.hp"
                        type="number"
                        min="1"
                        placeholder="35"
                        class="w-full"
                        required
                      />
                      <p v-if="props.errors?.hp" class="text-xs text-error mt-1">
                        {{ props.errors.hp }}
                      </p>
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-bold text-base-content/80 mb-2">
                        Attaque *
                      </label>
                      <Input
                        v-model="form.attack"
                        type="number"
                        min="1"
                        placeholder="55"
                        class="w-full"
                        required
                      />
                      <p v-if="props.errors?.attack" class="text-xs text-error mt-1">
                        {{ props.errors.attack }}
                      </p>
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-bold text-base-content/80 mb-2">
                        Défense *
                      </label>
                      <Input
                        v-model="form.defense"
                        type="number"
                        min="1"
                        placeholder="40"
                        class="w-full"
                        required
                      />
                      <p v-if="props.errors?.defense" class="text-xs text-error mt-1">
                        {{ props.errors.defense }}
                      </p>
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-bold text-base-content/80 mb-2">
                        Vitesse *
                      </label>
                      <Input
                        v-model="form.speed"
                        type="number"
                        min="1"
                        placeholder="90"
                        class="w-full"
                        required
                      />
                      <p v-if="props.errors?.speed" class="text-xs text-error mt-1">
                        {{ props.errors.speed }}
                      </p>
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-bold text-base-content/80 mb-2">
                        Att. Spé *
                      </label>
                      <Input
                        v-model="form.special_attack"
                        type="number"
                        min="1"
                        placeholder="50"
                        class="w-full"
                        required
                      />
                      <p v-if="props.errors?.special_attack" class="text-xs text-error mt-1">
                        {{ props.errors.special_attack }}
                      </p>
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-bold text-base-content/80 mb-2">
                        Déf. Spé *
                      </label>
                      <Input
                        v-model="form.special_defense"
                        type="number"
                        min="1"
                        placeholder="50"
                        class="w-full"
                        required
                      />
                      <p v-if="props.errors?.special_defense" class="text-xs text-error mt-1">
                        {{ props.errors.special_defense }}
                      </p>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Description
                  </label>
                  <textarea
                    v-model="form.description"
                    placeholder="Description du Pokémon..."
                    class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50"
                    rows="3"
                  ></textarea>
                  <p v-if="props.errors?.description" class="text-xs text-error mt-1">
                    {{ props.errors.description }}
                  </p>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    ⚔️ Résistances/Faiblesses
                  </h4>

                  <div v-for="(resistance, index) in form.resistances" :key="index" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                      <Select
                        v-model="resistance.name"
                        :options="typeOptions"
                        placeholder="Type"
                        class="w-full"
                      />
                    </div>
                    <div>
                      <Input
                        v-model="resistance.damage_multiplier"
                        type="number"
                        step="0.1"
                        placeholder="1.0"
                        class="w-full"
                      />
                    </div>
                    <div>
                      <Select
                        v-model="resistance.damage_relation"
                        :options="damageRelationOptions"
                        class="w-full"
                      />
                    </div>
                    <div class="flex gap-2">
                      <Button
                        @click="addResistance"
                        type="button"
                        variant="outline"
                        size="sm"
                      >
                        ➕
                      </Button>
                      <Button
                        v-if="form.resistances.length > 1"
                        @click="removeResistance(index)"
                        type="button"
                        variant="outline"
                        size="sm"
                        class="text-error"
                      >
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
                    {{ isSubmitting || form.processing ? 'Création...' : 'Créer le Pokémon' }}
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
                      <li>• <span class="text-base-content font-medium">Normal</span> : Standard</li>
                      <li>• <span class="text-info font-medium">Rare</span> : Peu commun</li>
                      <li>• <span class="text-warning font-medium">Épique</span> : Très rare</li>
                      <li>• <span class="text-error font-medium">Légendaire</span> : Unique</li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Statistiques :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• PV : Points de vie</li>
                      <li>• Attaque : Dégâts physiques</li>
                      <li>• Défense : Résistance physique</li>
                      <li>• Vitesse : Rapidité</li>
                      <li>• Att/Déf Spé : Magique</li>
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
                  <span class="text-xl">🔗</span>
                  NAVIGATION
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit('/admin/pokemons')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste Pokémon
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
