<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { getTypeColor, getRarityColor } from '@/utils/pokemon';

const props = defineProps<{
  rarities: string[];
  generations: number[];
  types: string[];
  damageRelations: string[];
}>();

const form = reactive({
  pokedex_id: '9999',
  name: 'Herbizarre',
  image: null as File | null,
  types: [
    { name: 'Plante', image: '/images/types/Plante.png' },
    { name: 'Poison', image: '/images/types/Poison.png' }
  ],
  resistances: [
    { name: 'Eau', damage_multiplier: 0.5, damage_relation: 'resistant' },
    { name: 'Électrik', damage_multiplier: 0.5, damage_relation: 'resistant' },
    { name: 'Plante', damage_multiplier: 0.5, damage_relation: 'resistant' },
    { name: 'Combat', damage_multiplier: 0.5, damage_relation: 'resistant' },
    { name: 'Fée', damage_multiplier: 0.5, damage_relation: 'resistant' },
    { name: 'Feu', damage_multiplier: 2, damage_relation: 'vulnerable' },
    { name: 'Glace', damage_multiplier: 2, damage_relation: 'vulnerable' },
    { name: 'Vol', damage_multiplier: 2, damage_relation: 'vulnerable' },
    { name: 'Psy', damage_multiplier: 2, damage_relation: 'vulnerable' }
  ],
  evolution_id: '',
  pre_evolution_id: '',
  description: 'Un Pokémon de type Plante/Poison qui évolue depuis Bulbizarre. Il est plus grand et plus fort que sa forme précédente.',
  height: '100',
  weight: '130',
  rarity: 'rare',
  is_shiny: false,
  hp: '60',
  attack: '62',
  defense: '63',
  speed: '60',
  special_attack: '80',
  special_defense: '80',
  generation: '1',
  processing: false,
  errors: {} as Record<string, string>,
});

const addType = () => {
  form.types.push({ name: '', image: '' });
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

const getTypeImage = (typeName: string) => {
  return `/images/types/${typeName}.png`;
};

const updateTypeImage = (index: number) => {
  const type = form.types[index];
  if (type.name) {
    type.image = getTypeImage(type.name);
  }
};

const getRarityLabel = (rarity: string) => {
  const labels: Record<string, string> = {
    normal: 'Normal',
    rare: 'Rare',
    epic: 'Épique',
    legendary: 'Légendaire'
  };
  return labels[rarity] || rarity;
};

const getDamageRelationLabel = (relation: string) => {
  const labels: Record<string, string> = {
    neutral: 'Neutre',
    resistant: 'Résistant',
    vulnerable: 'Vulnérable',
    twice_resistant: 'Double Résistant'
  };
  return labels[relation] || relation;
};

const getTotalStats = () => {
  const stats = [
    parseInt(form.hp) || 0,
    parseInt(form.attack) || 0,
    parseInt(form.defense) || 0,
    parseInt(form.speed) || 0,
    parseInt(form.special_attack) || 0,
    parseInt(form.special_defense) || 0
  ];
  return stats.reduce((sum, stat) => sum + stat, 0);
};

const calculateCP = () => {
  const baseCP = getTotalStats();
  let finalCP = baseCP;
  
  if (form.is_shiny) {
    finalCP = Math.floor(finalCP * 1.1);
  }
  
  const multipliers: Record<string, number> = {
    normal: 1.10,
    rare: 1.50,
    epic: 2.25,
    legendary: 4.0
  };
  const rarityMultiplier = multipliers[form.rarity] || 1.10;
  finalCP = Math.floor(finalCP * rarityMultiplier);
  
  return finalCP * 10;
};

const submit = () => {
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
      form.processing = false;
    },
    onError: (errors: Record<string, string>) => {
      form.processing = false;
      form.errors = errors;
    },
    preserveScroll: true,
  });
};

const resetForm = () => {
  form.pokedex_id = '';
  form.name = '';
  form.image = null;
  form.types = [{ name: '', image: '' }];
  form.resistances = [{ name: '', damage_multiplier: 1, damage_relation: 'neutral' }];
  form.evolution_id = '';
  form.pre_evolution_id = '';
  form.description = '';
  form.height = '';
  form.weight = '';
  form.rarity = 'normal';
  form.is_shiny = false;
  form.hp = '';
  form.attack = '';
  form.defense = '';
  form.speed = '';
  form.special_attack = '';
  form.special_defense = '';
  form.generation = '';
  form.errors = {};
};
</script>

<template>
  <Head title="Créer un Pokémon" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent mb-1 tracking-wider">
            ➕ CRÉER UN POKÉMON
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Nouveau Pokémon en base de données
          </p>
        </div>
      </div>

      <div v-if="form.errors.error" class="mx-auto max-w-4xl mb-4">
        <div class="alert alert-error">
          <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ form.errors.error }}</span>
        </div>
      </div>

      <div class="absolute left-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">ℹ️</span>
              AIDE
            </h3>
          </div>
          <div class="p-3 text-xs text-base-content/70 space-y-2">
            <p>• Pokédex ID : numéro officiel</p>
            <p>• Nom : 3-50 caractères</p>
            <p>• Image : format GIF recommandé</p>
            <p>• Types : au moins 1 requis</p>
            <p>• Stats : 1-255 chacune</p>
            <p>• Rareté : normal, rare, épique, légendaire</p>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🔄</span>
              ACTIONS
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button
              @click="resetForm"
              variant="outline"
              size="sm"
              class="w-full"
              :disabled="form.processing"
            >
              🗑️ Vider le formulaire
            </Button>
            <div class="border-t border-base-300/30 pt-2">
              <Button
                @click="router.visit('/admin/pokemons')"
                variant="ghost"
                size="sm"
                class="w-full"
              >
                ← Liste Pokémon
              </Button>
              <Button
                @click="router.visit('/admin')"
                variant="ghost"
                size="sm"
                class="w-full mt-1"
              >
                🏠 Dashboard
              </Button>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📋</span>
              APERÇU
            </h3>
          </div>
          <div class="p-3 space-y-3">
            <div class="text-center">
              <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-error/20 to-error/10 flex items-center justify-center text-xl font-bold mb-2">
                {{ form.name ? form.name.charAt(0).toUpperCase() : '?' }}
              </div>
              <div class="text-sm font-semibold">{{ form.name || 'Nouveau Pokémon' }}</div>
              <div class="text-xs text-base-content/60">#{{ form.pokedex_id || '000' }}</div>
            </div>
            
            <div class="space-y-2 text-xs">
              <div class="flex justify-between">
                <span class="text-base-content/70">Rareté:</span>
                <span :class="`badge badge-xs bg-gradient-to-r ${getRarityColor(form.rarity)} text-white border-0`">
                  {{ getRarityLabel(form.rarity) }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-base-content/70">Génération:</span>
                <span class="font-semibold">{{ form.generation || '-' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-base-content/70">Stats Total:</span>
                <span class="font-semibold text-success">{{ getTotalStats() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-base-content/70">CP:</span>
                <span class="font-semibold text-warning">{{ calculateCP().toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-base-content/70">Shiny:</span>
                <span class="font-semibold">{{ form.is_shiny ? '✨ Oui' : 'Non' }}</span>
              </div>
            </div>

            <div v-if="form.types.some(t => t.name)" class="space-y-1">
              <div class="text-xs text-base-content/70">Types:</div>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="type in form.types.filter(t => t.name)"
                  :key="type.name"
                  :class="`badge badge-xs bg-gradient-to-r ${getTypeColor(type.name)} text-white border-0`"
                >
                  {{ type.name }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[800px] h-[700px]">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📝</span>
              FORMULAIRE DE CRÉATION
            </h3>
          </div>

          <form @submit.prevent="submit" class="flex-1 overflow-y-auto p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <!-- Informations de base -->
              <div class="space-y-4">
                <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm">Informations de base</h4>
                
                <div>
                  <label for="pokedex_id" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                    Pokédex ID <span class="text-error">*</span>
                  </label>
                  <Input
                    id="pokedex_id"
                    v-model="form.pokedex_id"
                    type="number"
                    required
                    placeholder="1"
                    :class="{ 'border-error': form.errors.pokedex_id }"
                  />
                  <p v-if="form.errors.pokedex_id" class="mt-1 text-sm text-error">{{ form.errors.pokedex_id }}</p>
                </div>

                <div>
                  <label for="name" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                    Nom <span class="text-error">*</span>
                  </label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    placeholder="Bulbizarre"
                    :class="{ 'border-error': form.errors.name }"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-error">{{ form.errors.name }}</p>
                </div>

                <div>
                  <label for="image" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                    Image <span class="text-error">*</span>
                  </label>
                  <input
                    id="image"
                    type="file"
                    @change="(event) => { 
                      const target = event.target as HTMLInputElement; 
                      if (target.files && target.files[0]) {
                        form.image = target.files[0];
                      }
                    }"
                    accept="image/*"
                    required
                    class="file-input file-input-bordered w-full bg-base-100/80 border-base-300/50"
                    :class="{ 'border-error': form.errors.image }"
                  />
                  <p v-if="form.errors.image" class="mt-1 text-sm text-error">{{ form.errors.image }}</p>
                </div>

                <div>
                  <label for="description" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                    Description <span class="text-error">*</span>
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    required
                    placeholder="Description du Pokémon..."
                    class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50"
                    :class="{ 'border-error': form.errors.description }"
                    rows="3"
                  ></textarea>
                  <p v-if="form.errors.description" class="mt-1 text-sm text-error">{{ form.errors.description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="height" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Taille <span class="text-error">*</span>
                    </label>
                    <Input
                      id="height"
                      v-model="form.height"
                      type="number"
                      required
                      placeholder="70"
                      :class="{ 'border-error': form.errors.height }"
                    />
                    <p v-if="form.errors.height" class="mt-1 text-sm text-error">{{ form.errors.height }}</p>
                  </div>

                  <div>
                    <label for="weight" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Poids <span class="text-error">*</span>
                    </label>
                    <Input
                      id="weight"
                      v-model="form.weight"
                      type="number"
                      required
                      placeholder="69"
                      :class="{ 'border-error': form.errors.weight }"
                    />
                    <p v-if="form.errors.weight" class="mt-1 text-sm text-error">{{ form.errors.weight }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="rarity" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Rareté <span class="text-error">*</span>
                    </label>
                    <select
                      id="rarity"
                      v-model="form.rarity"
                      required
                      class="select select-bordered w-full bg-base-100/80 border-base-300/50"
                      :class="{ 'border-error': form.errors.rarity }"
                    >
                      <option v-for="rarity in rarities" :key="rarity" :value="rarity">
                        {{ getRarityLabel(rarity) }}
                      </option>
                    </select>
                    <p v-if="form.errors.rarity" class="mt-1 text-sm text-error">{{ form.errors.rarity }}</p>
                  </div>

                  <div>
                    <label for="generation" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Génération
                    </label>
                    <select
                      id="generation"
                      v-model="form.generation"
                      class="select select-bordered w-full bg-base-100/80 border-base-300/50"
                      :class="{ 'border-error': form.errors.generation }"
                    >
                      <option value="">Aucune</option>
                      <option v-for="gen in generations" :key="gen" :value="gen">
                        {{ gen }}
                      </option>
                    </select>
                    <p v-if="form.errors.generation" class="mt-1 text-sm text-error">{{ form.errors.generation }}</p>
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <input
                    id="is_shiny"
                    v-model="form.is_shiny"
                    type="checkbox"
                    class="checkbox checkbox-sm"
                  />
                  <label for="is_shiny" class="text-sm font-bold text-base-content/70 uppercase tracking-wider">
                    Pokémon Shiny
                  </label>
                </div>
              </div>

              <!-- Statistiques -->
              <div class="space-y-4">
                <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm">Statistiques</h4>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="hp" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      PV <span class="text-error">*</span>
                    </label>
                    <Input
                      id="hp"
                      v-model="form.hp"
                      type="number"
                      min="1"
                      max="255"
                      required
                      placeholder="45"
                      :class="{ 'border-error': form.errors.hp }"
                    />
                    <p v-if="form.errors.hp" class="mt-1 text-sm text-error">{{ form.errors.hp }}</p>
                  </div>

                  <div>
                    <label for="attack" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Attaque <span class="text-error">*</span>
                    </label>
                    <Input
                      id="attack"
                      v-model="form.attack"
                      type="number"
                      min="1"
                      max="255"
                      required
                      placeholder="49"
                      :class="{ 'border-error': form.errors.attack }"
                    />
                    <p v-if="form.errors.attack" class="mt-1 text-sm text-error">{{ form.errors.attack }}</p>
                  </div>

                  <div>
                    <label for="defense" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Défense <span class="text-error">*</span>
                    </label>
                    <Input
                      id="defense"
                      v-model="form.defense"
                      type="number"
                      min="1"
                      max="255"
                      required
                      placeholder="49"
                      :class="{ 'border-error': form.errors.defense }"
                    />
                    <p v-if="form.errors.defense" class="mt-1 text-sm text-error">{{ form.errors.defense }}</p>
                  </div>

                  <div>
                    <label for="speed" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Vitesse <span class="text-error">*</span>
                    </label>
                    <Input
                      id="speed"
                      v-model="form.speed"
                      type="number"
                      min="1"
                      max="255"
                      required
                      placeholder="45"
                      :class="{ 'border-error': form.errors.speed }"
                    />
                    <p v-if="form.errors.speed" class="mt-1 text-sm text-error">{{ form.errors.speed }}</p>
                  </div>

                  <div>
                    <label for="special_attack" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Att. Spé. <span class="text-error">*</span>
                    </label>
                    <Input
                      id="special_attack"
                      v-model="form.special_attack"
                      type="number"
                      min="1"
                      max="255"
                      required
                      placeholder="65"
                      :class="{ 'border-error': form.errors.special_attack }"
                    />
                    <p v-if="form.errors.special_attack" class="mt-1 text-sm text-error">{{ form.errors.special_attack }}</p>
                  </div>

                  <div>
                    <label for="special_defense" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Déf. Spé. <span class="text-error">*</span>
                    </label>
                    <Input
                      id="special_defense"
                      v-model="form.special_defense"
                      type="number"
                      min="1"
                      max="255"
                      required
                      placeholder="65"
                      :class="{ 'border-error': form.errors.special_defense }"
                    />
                    <p v-if="form.errors.special_defense" class="mt-1 text-sm text-error">{{ form.errors.special_defense }}</p>
                  </div>
                </div>

                <div class="bg-base-200/30 rounded-lg p-3">
                  <div class="text-center">
                    <div class="text-sm text-base-content/70">Total des statistiques</div>
                    <div class="text-2xl font-bold text-success">{{ getTotalStats() }}</div>
                  </div>
                </div>
                <div class="bg-base-200/30 rounded-lg p-3 mt-2">
                  <div class="text-center">
                    <div class="text-sm text-base-content/70">CP</div>
                    <div class="text-2xl font-bold text-warning">{{ calculateCP().toLocaleString() }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Types -->
            <div class="mt-8">
              <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm">Types</h4>
                <Button @click="addType" type="button" size="sm" variant="outline">
                  ➕ Ajouter un type
                </Button>
              </div>
              
              <div class="space-y-3">
                <div
                  v-for="(type, index) in form.types"
                  :key="index"
                  class="flex items-center gap-3"
                >
                  <select
                    v-model="type.name"
                    @change="updateTypeImage(index)"
                    class="select select-bordered flex-1 bg-base-100/80 border-base-300/50"
                    :class="{ 'border-error': form.errors[`types.${index}.name`] }"
                  >
                    <option value="">Sélectionner un type</option>
                    <option v-for="typeOption in types" :key="typeOption" :value="typeOption">
                      {{ typeOption }}
                    </option>
                  </select>
                  
                  <Button
                    @click="removeType(index)"
                    type="button"
                    size="sm"
                    variant="ghost"
                    class="text-error"
                    :disabled="form.types.length === 1"
                  >
                    🗑️
                  </Button>
                </div>
              </div>
            </div>

            <!-- Résistances -->
            <div class="mt-8">
              <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm">Résistances</h4>
                <Button @click="addResistance" type="button" size="sm" variant="outline">
                  ➕ Ajouter une résistance
                </Button>
              </div>
              
              <div class="space-y-3">
                <div
                  v-for="(resistance, index) in form.resistances"
                  :key="index"
                  class="grid grid-cols-3 gap-3"
                >
                  <select
                    v-model="resistance.name"
                    class="select select-bordered bg-base-100/80 border-base-300/50"
                    :class="{ 'border-error': form.errors[`resistances.${index}.name`] }"
                  >
                    <option value="">Sélectionner un type</option>
                    <option v-for="typeOption in types" :key="typeOption" :value="typeOption">
                      {{ typeOption }}
                    </option>
                  </select>
                  
                  <Input
                    v-model.number="resistance.damage_multiplier"
                    type="number"
                    step="0.1"
                    placeholder="1.0"
                    class="text-center"
                    :class="{ 'border-error': form.errors[`resistances.${index}.damage_multiplier`] }"
                  />
                  
                  <div class="flex gap-2">
                    <select
                      v-model="resistance.damage_relation"
                      class="select select-bordered flex-1 bg-base-100/80 border-base-300/50"
                      :class="{ 'border-error': form.errors[`resistances.${index}.damage_relation`] }"
                    >
                      <option v-for="relation in damageRelations" :key="relation" :value="relation">
                        {{ getDamageRelationLabel(relation) }}
                      </option>
                    </select>
                    
                    <Button
                      @click="removeResistance(index)"
                      type="button"
                      size="sm"
                      variant="ghost"
                      class="text-error"
                      :disabled="form.resistances.length === 1"
                    >
                      🗑️
                    </Button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Évolutions -->
            <div class="mt-8">
              <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm mb-4">Évolutions</h4>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="evolution_id" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                    Évolution
                  </label>
                  <Input
                    id="evolution_id"
                    v-model="form.evolution_id"
                    type="number"
                    placeholder="ID du Pokémon évolué"
                    :class="{ 'border-error': form.errors.evolution_id }"
                  />
                  <p v-if="form.errors.evolution_id" class="mt-1 text-sm text-error">{{ form.errors.evolution_id }}</p>
                </div>

                <div>
                  <label for="pre_evolution_id" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                    Pré-évolution
                  </label>
                  <Input
                    id="pre_evolution_id"
                    v-model="form.pre_evolution_id"
                    type="number"
                    placeholder="ID du Pokémon de base"
                    :class="{ 'border-error': form.errors.pre_evolution_id }"
                  />
                  <p v-if="form.errors.pre_evolution_id" class="mt-1 text-sm text-error">{{ form.errors.pre_evolution_id }}</p>
                </div>
              </div>
            </div>

            <!-- Submit -->
            <div class="mt-8 flex justify-end gap-3">
              <Button
                @click="router.visit('/admin/pokemons')"
                type="button"
                variant="ghost"
                :disabled="form.processing"
              >
                Annuler
              </Button>
              <Button
                type="submit"
                variant="primary"
                :disabled="form.processing"
              >
                <span v-if="form.processing">⏳</span>
                <span v-else>💾</span>
                {{ form.processing ? 'Création...' : 'Créer le Pokémon' }}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template> 