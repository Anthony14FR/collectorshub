<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import Input from "@/Components/UI/Input.vue";
import Select from "@/Components/UI/Select.vue";
import Alert from "@/Components/UI/Alert.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";
import { Zap, ArrowLeft, Plus, Save, Loader2, Info, RotateCcw, Home, Package, BarChart3, FileImage, Type, Shield, DollarSign } from 'lucide-vue-next';

const props = defineProps({
  auth: Object,
  errors: Object,
});

const form = ref({
  name: "",
  description: "",
  type: "ball",
  effect: {},
  price: 0,
  rarity: "normal",
  image: null,
});

const isLoading = ref(false);

const typeOptions = [
  { value: "ball", label: "Ball" },
  { value: "avatar", label: "Avatar" },
  { value: "background", label: "Background" },
];

const rarityOptions = [
  { value: "normal", label: "Normal" },
  { value: "rare", label: "Rare" },
  { value: "epic", label: "Épique" },
  { value: "legendary", label: "Légendaire" },
];

const effectFields = computed(() => {
  switch (form.value.type) {
  default:
    return [];
  }
});

const updateEffect = (field, value) => {
  form.value.effect = {
    ...form.value.effect,
    [field]: value,
  };
};

const submit = () => {
  isLoading.value = true;

  const formData = new FormData();
  formData.append('name', form.value.name);
  formData.append('description', form.value.description);
  formData.append('type', form.value.type);
  formData.append('price', form.value.price);
  formData.append('rarity', form.value.rarity);
  formData.append('effect', JSON.stringify(form.value.effect));

  if (form.value.image) {
    formData.append('image', form.value.image);
  }

  router.post("/admin/items", formData, {
    onSuccess: () => {
    },
    onError: () => {
      isLoading.value = false;
    },
  });
};
</script>

<template>
  <Head title="Créer un Item" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Zap" :size="28" class="inline align-middle mr-2" />
            CRÉER UN ITEM
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Ajouter un nouvel item au shop
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
                  <Link href="/admin/items">
                    <Button variant="outline" size="sm" class="w-full justify-start">
                      <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour aux items
                    </Button>
                  </Link>
                  <Link href="/admin">
                    <Button variant="ghost" size="sm" class="w-full justify-start">
                      <component :is="Home" :size="16" class="mr-2" /> Dashboard
                    </Button>
                  </Link>
                  <Button @click="form = { name: '', description: '', type: 'ball', effect: {}, price: 0, rarity: 'normal', image: null }" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="RotateCcw" :size="16" class="mr-2" /> Réinitialiser
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
                  <div class="text-xs text-base-content/70">
                    <p class="mb-2"><strong>Types d'items :</strong></p>
                    <ul class="space-y-1 text-xs">
                      <li>• <strong>Ball :</strong> Pour capturer des Pokémon</li>
                      <li>• <strong>Avatar :</strong> Image de profil</li>
                      <li>• <strong>Background :</strong> Arrière-plan</li>
                    </ul>
                  </div>
                  <div class="text-xs text-base-content/70">
                    <p class="mb-2"><strong>Raretés :</strong></p>
                    <ul class="space-y-1 text-xs">
                      <li>• <strong>Normal :</strong> Commun</li>
                      <li>• <strong>Rare :</strong> Peu commun</li>
                      <li>• <strong>Épique :</strong> Très rare</li>
                      <li>• <strong>Légendaire :</strong> Exceptionnel</li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="BarChart3" :size="18" />
                    APERÇU
                  </h3>
                </div>
                <div class="p-4">
                  <div class="bg-base-200/50 rounded-lg p-3 space-y-2">
                    <div class="font-bold text-sm text-warning line-clamp-2">
                      {{ form.name || 'Nom de l\'item...' }}
                    </div>
                    <div class="text-xs text-base-content/70 line-clamp-2">
                      {{ form.description || 'Description...' }}
                    </div>
                    <div class="flex justify-between items-center text-xs">
                      <span class="text-primary font-bold">{{ form.price }} ₽</span>
                      <span class="text-base-content/50">{{ form.type }}</span>
                    </div>
                    <div class="text-xs text-base-content/50 border-t border-base-300/30 pt-2">
                      Rareté: {{ form.rarity }}
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
                  <component :is="Plus" :size="18" />
                  CRÉER UN ITEM
                </h3>
              </div>

              <form @submit.prevent="submit" class="flex-1 flex flex-col">
                <div class="flex-1 p-6 space-y-6 min-h-0 overflow-y-auto">

                  <div class="space-y-4">
                    <h3 class="text-sm font-bold tracking-wider text-base-content/80 uppercase flex items-center gap-2">
                      <component :is="Package" :size="16" />
                      INFORMATIONS DE BASE
                    </h3>

                    <Input
                      v-model="form.name"
                      label="Nom de l'item"
                      placeholder="Ex: Master Ball"
                      :error="errors?.name"
                      required
                      maxlength="50"
                    />

                    <Input
                      v-model="form.description"
                      label="Description"
                      placeholder="Ex: Une Pokéball très rare qui capture à coup sûr"
                      :error="errors?.description"
                      maxlength="250"
                      type="textarea"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <Select
                        v-model="form.type"
                        label="Type d'item"
                        :options="typeOptions"
                        :error="errors?.type"
                        required
                      />

                      <Select
                        v-model="form.rarity"
                        label="Rareté"
                        :options="rarityOptions"
                        :error="errors?.rarity"
                        required
                      />
                    </div>

                    <Input
                      v-model.number="form.price"
                      label="Prix"
                      type="number"
                      placeholder="0"
                      :error="errors?.price"
                      required
                      min="0"
                    />

                    <div class="space-y-2">
                      <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase flex items-center gap-2">
                        <component :is="FileImage" :size="16" />
                        IMAGE
                      </label>
                      <input
                        type="file"
                        accept="image/*"
                        @change="form.image = $event.target.files[0]"
                        class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-content hover:file:bg-primary-focus"
                      />
                      <p v-if="errors?.image" class="text-xs text-error">
                        {{ errors.image }}
                      </p>
                    </div>
                  </div>

                  <div v-if="effectFields.length > 0" class="space-y-4">
                    <h3 class="text-sm font-bold tracking-wider text-base-content/80 uppercase flex items-center gap-2">
                      <component :is="Shield" :size="16" />
                      EFFETS
                    </h3>

                    <div v-for="field in effectFields" :key="field.name" class="space-y-2">
                      <label class="block text-sm font-medium text-base-content">
                        {{ field.label }}
                        <span v-if="field.required" class="text-error">*</span>
                      </label>

                      <Select
                        v-if="field.type === 'select'"
                        :model-value="form.effect[field.name]"
                        :options="field.options"
                        :error="errors?.[`effect.${field.name}`]"
                        @update:model-value="updateEffect(field.name, $event)"
                        :required="field.required"
                      />

                      <Input
                        v-else
                        :model-value="form.effect[field.name]"
                        :type="field.type"
                        :placeholder="field.label"
                        :error="errors?.[`effect.${field.name}`]"
                        @update:model-value="updateEffect(field.name, $event)"
                        :required="field.required"
                        :min="field.min"
                        :max="field.max"
                      />
                    </div>
                  </div>
                </div>

                <div class="shrink-0 p-4 bg-gradient-to-r from-primary/5 to-secondary/5 border-t border-primary/20">
                  <div class="flex gap-3">
                    <Link href="/admin/items">
                      <Button variant="outline" size="sm" class="flex-1">
                        <component :is="ArrowLeft" :size="16" class="mr-2" />
                        Annuler
                      </Button>
                    </Link>
                    <Button variant="primary" size="sm" type="submit" class="flex-1" :loading="isLoading">
                      <component :is="isLoading ? Loader2 : Save" :size="16" class="mr-2" :class="{ 'animate-spin': isLoading }" />
                      {{ isLoading ? 'Création...' : 'Créer l\'item' }}
                    </Button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
