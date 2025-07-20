<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import { getRarityColor, getTypeColor } from '@/utils/pokemon';
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Zap, ArrowLeft, Plus, X, Save, Loader2, Info, RotateCcw, Home, Hash, Star, Sparkles, BarChart3, FileImage, Type, Shield, Zap as ZapIcon } from 'lucide-vue-next';

const { rarities, generations, types, damageRelations } = defineProps<{
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

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Zap" :size="28" class="inline align-middle mr-2" />
            CRÉER UN POKÉMON
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Nouveau Pokémon en base de données
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
                  <Button @click="router.visit('/admin/pokemons')" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour
                  </Button>
                  <Button @click="router.visit('/admin')" variant="ghost" size="sm" class="w-full justify-start">
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
                    <component :is="BarChart3" :size="18" />
                    APERÇU
                  </h3>
                </div>
                <div class="p-3 space-y-3">
                  <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center text-xl font-bold mb-2">
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
                      <span class="font-semibold">{{ form.is_shiny ? 'Oui' : 'Non' }}</span>
                    </div>
                  </div>

                  <div v-if="form.types.some(t => t.name)" class="space-y-1">
                    <div class="text-xs text-base-content/70">Types:</div>
                    <div class="flex flex-wrap gap-1">
                      <span v-for="type in form.types.filter(t => t.name)" :key="type.name"
                            :class="`badge badge-xs bg-gradient-to-r ${getTypeColor(type.name)} text-white border-0`">
                        {{ type.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="Zap" :size="18" />
                  FORMULAIRE DE CRÉATION
                </h3>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <div v-if="form.errors.error" class="mb-4">
                  <div class="alert alert-error">
                    <component :is="Info" :size="20" />
                    <span>{{ form.errors.error }}</span>
                  </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                  <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content">Informations de base</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <Input
                        label="Pokédex ID"
                        v-model="form.pokedex_id"
                        type="number"
                        required
                        placeholder="1"
                        :error="form.errors.pokedex_id"
                      />
                      <Input
                        label="Nom"
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="Bulbizarre"
                        :error="form.errors.name"
                      />
                      <div>
                        <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-1">
                          <component :is="FileImage" :size="14" class="mr-1" />
                          Image
                        </label>
                        <input
                          type="file"
                          @change="(event) => {
                            const target = event.target as HTMLInputElement;
                            if (target.files && target.files[0]) {
                              form.image = target.files[0];
                            }
                          }"
                          accept="image/*"
                          required
                          class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-content hover:file:bg-primary-focus"
                          :class="{ 'border-error': form.errors.image }"
                        />
                        <p v-if="form.errors.image" class="mt-1 text-xs text-error">{{ form.errors.image }}</p>
                      </div>
                      <div>
                        <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-1">
                          Description
                        </label>
                        <textarea
                          v-model="form.description"
                          required
                          placeholder="Description du Pokémon..."
                          class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none resize-none"
                          :class="{ 'border-error': form.errors.description }"
                          rows="3"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-xs text-error">{{ form.errors.description }}</p>
                      </div>
                      <Input
                        label="Taille"
                        v-model="form.height"
                        type="number"
                        required
                        placeholder="70"
                        :error="form.errors.height"
                      />
                      <Input
                        label="Poids"
                        v-model="form.weight"
                        type="number"
                        required
                        placeholder="69"
                        :error="form.errors.weight"
                      />
                      <div>
                        <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-1">
                          Rareté
                        </label>
                        <select
                          v-model="form.rarity"
                          required
                          class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none"
                          :class="{ 'border-error': form.errors.rarity }"
                        >
                          <option v-for="rarity in rarities" :key="rarity" :value="rarity">
                            {{ getRarityLabel(rarity) }}
                          </option>
                        </select>
                        <p v-if="form.errors.rarity" class="mt-1 text-xs text-error">{{ form.errors.rarity }}</p>
                      </div>
                      <div>
                        <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-1">
                          Génération
                        </label>
                        <select
                          v-model="form.generation"
                          class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none"
                          :class="{ 'border-error': form.errors.generation }"
                        >
                          <option value="">Aucune</option>
                          <option v-for="gen in generations" :key="gen" :value="gen">
                            {{ gen }}
                          </option>
                        </select>
                        <p v-if="form.errors.generation" class="mt-1 text-xs text-error">{{ form.errors.generation }}</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <input
                        id="is_shiny"
                        v-model="form.is_shiny"
                        type="checkbox"
                        class="checkbox checkbox-sm"
                      />
                      <label for="is_shiny" class="text-sm font-bold text-base-content/70 uppercase tracking-wider flex items-center gap-1">
                        <component :is="Sparkles" :size="14" />
                        Pokémon Shiny
                      </label>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content">Statistiques</h3>
                    <div class="grid grid-cols-2 gap-4">
                      <Input
                        label="PV"
                        v-model="form.hp"
                        type="number"
                        min="1"
                        max="255"
                        required
                        placeholder="45"
                        :error="form.errors.hp"
                      />
                      <Input
                        label="Attaque"
                        v-model="form.attack"
                        type="number"
                        min="1"
                        max="255"
                        required
                        placeholder="49"
                        :error="form.errors.attack"
                      />
                      <Input
                        label="Défense"
                        v-model="form.defense"
                        type="number"
                        min="1"
                        max="255"
                        required
                        placeholder="49"
                        :error="form.errors.defense"
                      />
                      <Input
                        label="Vitesse"
                        v-model="form.speed"
                        type="number"
                        min="1"
                        max="255"
                        required
                        placeholder="45"
                        :error="form.errors.speed"
                      />
                      <Input
                        label="Att. Spé."
                        v-model="form.special_attack"
                        type="number"
                        min="1"
                        max="255"
                        required
                        placeholder="65"
                        :error="form.errors.special_attack"
                      />
                      <Input
                        label="Déf. Spé."
                        v-model="form.special_defense"
                        type="number"
                        min="1"
                        max="255"
                        required
                        placeholder="65"
                        :error="form.errors.special_defense"
                      />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                      <div class="bg-base-200/30 rounded-lg p-3">
                        <div class="text-center">
                          <div class="text-sm text-base-content/70">Total des statistiques</div>
                          <div class="text-2xl font-bold text-success">{{ getTotalStats() }}</div>
                        </div>
                      </div>
                      <div class="bg-base-200/30 rounded-lg p-3">
                        <div class="text-center">
                          <div class="text-sm text-base-content/70">CP</div>
                          <div class="text-2xl font-bold text-warning">{{ calculateCP().toLocaleString() }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <h3 class="text-lg font-semibold text-base-content flex items-center gap-2">
                        <component :is="Type" :size="20" />
                        Types
                      </h3>
                      <Button @click="addType" type="button" size="sm" variant="outline">
                        <component :is="Plus" :size="16" class="mr-2" />
                        Ajouter un type
                      </Button>
                    </div>
                    <div class="space-y-3">
                      <div v-for="(type, index) in form.types" :key="index" class="flex items-center gap-3">
                        <select
                          v-model="type.name"
                          @change="updateTypeImage(index)"
                          class="flex-1 px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none"
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
                          <component :is="X" :size="16" />
                        </Button>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <h3 class="text-lg font-semibold text-base-content flex items-center gap-2">
                        <component :is="Shield" :size="20" />
                        Résistances
                      </h3>
                      <Button @click="addResistance" type="button" size="sm" variant="outline">
                        <component :is="Plus" :size="16" class="mr-2" />
                        Ajouter une résistance
                      </Button>
                    </div>
                    <div class="space-y-3">
                      <div v-for="(resistance, index) in form.resistances" :key="index" class="grid grid-cols-3 gap-3">
                        <select
                          v-model="resistance.name"
                          class="px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none"
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
                          :error="form.errors[`resistances.${index}.damage_multiplier`]"
                        />
                        <div class="flex gap-2">
                          <select
                            v-model="resistance.damage_relation"
                            class="flex-1 px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none"
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
                            <component :is="X" :size="16" />
                          </Button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content">Évolutions</h3>
                    <div class="grid grid-cols-2 gap-4">
                      <Input
                        label="Évolution"
                        v-model="form.evolution_id"
                        type="number"
                        placeholder="ID du Pokémon évolué"
                        :error="form.errors.evolution_id"
                      />
                      <Input
                        label="Pré-évolution"
                        v-model="form.pre_evolution_id"
                        type="number"
                        placeholder="ID du Pokémon de base"
                        :error="form.errors.pre_evolution_id"
                      />
                    </div>
                  </div>

                  <div class="flex justify-end gap-3 mt-8">
                    <Button @click="resetForm" variant="outline" :disabled="form.processing">
                      <component :is="RotateCcw" :size="16" class="mr-2" />
                      Vider le formulaire
                    </Button>
                    <Button @click="router.visit('/admin/pokemons')" variant="ghost" :disabled="form.processing">
                      Annuler
                    </Button>
                    <Button type="submit" variant="primary" :loading="form.processing">
                      <component :is="form.processing ? Loader2 : Save" :size="16" class="mr-2" :class="{ 'animate-spin': form.processing }" />
                      {{ form.processing ? 'Création...' : 'Créer le Pokémon' }}
                    </Button>
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
