<script setup>
import { reactive, ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import Input from "@/Components/UI/Input.vue";
import Select from "@/Components/UI/Select.vue";
import Alert from "@/Components/UI/Alert.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";
import { Trophy, ArrowLeft, Plus, X, FileImage } from 'lucide-vue-next';

const props = defineProps({
  auth: Object,
  errors: Object,
});

const form = reactive({
  title: "",
  description: "",
  type: "",
  reward: "",
  image: null,
});

const isLoading = ref(false);
const imagePreview = ref(null);

const typeOptions = [
  { value: "pokedex", label: "Pokédex" },
  { value: "capture", label: "Capture" },
  { value: "rarity", label: "Rareté" },
  { value: "friend", label: "Ami" },
  { value: "tower", label: "Tour" },
  { value: "expedition", label: "Expédition" },
];

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.image = file;
    const reader = new FileReader();
    reader.onload = (event) => {
      imagePreview.value = event.target.result;
    };
    reader.readAsDataURL(file);
  } else {
    form.image = null;
    imagePreview.value = null;
  }
};

const requirementOptions = [
  { value: "count", label: "Nombre" },
  { value: "shiny", label: "Shiny)" },
  { value: "rarity", label: "Rareté" },
  { value: "level", label: "Niveau/étage" },
  { value: "types", label: 'Types' },
  { value: "pokemon_id", label: "ID Pokémon" },
];
const rarityOptions = [
  { value: "normal", label: "Normal" },
  { value: "rare", label: "Rare" },
  { value: "epic", label: "Épique" },
  { value: "legendary", label: "Légendaire" },
];
const shinyOptions = [
  { value: true, label: "Oui" },
  { value: false, label: "Non" },
];
const requirements = ref([]);
const newRequirementKey = ref(requirementOptions[0].value);
const newRequirementValue = ref("");
const typeKey = ref("");
const typeValue = ref("");
const typesList = ref([]);

const addTypeToTypes = () => {
  if (!typeKey.value || !typeValue.value) return;
  if (typesList.value.some((t) => t.key === typeKey.value)) return;
  typesList.value.push({
    key: typeKey.value,
    value: Number(typeValue.value),
  });
  typeKey.value = "";
  typeValue.value = "";
};
const removeTypeFromTypes = (key) => {
  typesList.value = typesList.value.filter((t) => t.key !== key);
};
const addRequirement = () => {
  if (!newRequirementKey.value) return;
  let value = newRequirementValue.value;
  if (
    newRequirementKey.value === "count" ||
    newRequirementKey.value === "level" ||
    newRequirementKey.value === "pokemon_id"
  ) {
    value = Number(newRequirementValue.value);
  } else if (newRequirementKey.value === "shiny") {
    value =
      newRequirementValue.value === "true" ||
      newRequirementValue.value === true;
  } else if (newRequirementKey.value === "types") {
    if (!typesList.value.length) return;
    value = {};
    typesList.value.forEach((t) => {
      value[t.key] = t.value;
    });
    typesList.value = [];
  }
  const idx = requirements.value.findIndex(
    (r) => r.key === newRequirementKey.value
  );
  if (idx !== -1) {
    requirements.value[idx].value = value;
  } else {
    requirements.value.push({ key: newRequirementKey.value, value });
  }
  newRequirementValue.value = "";
};
const removeRequirement = (key) => {
  requirements.value = requirements.value.filter((r) => r.key !== key);
};
watch(
  requirements,
  (val) => {
    const obj = {};
    val.forEach((r) => (obj[r.key] = r.value));
    form.requirements = JSON.stringify(obj);
  },
  { deep: true }
);

const submit = () => {
  isLoading.value = true;
  const formData = new FormData();
  formData.append("title", form.title);
  formData.append("description", form.description);
  formData.append("type", form.type);
  formData.append("cash_reward", form.cash_reward ?? 0);
  formData.append("xp_reward", form.xp_reward ?? 0);
  formData.append("requirements", form.requirements ?? "{}");
  if (form.image) {
    formData.append("image", form.image);
  }
  router.post("/admin/success", formData, {
    onSuccess: () => {
      isLoading.value = false;
    },
    onError: () => {
      isLoading.value = false;
    },
  });
};
</script>

<template>
  <Head title="Créer un Succès" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Trophy" :size="28" class="inline align-middle mr-2" />
            CRÉER UN SUCCÈS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Ajouter un nouveau succès/badge
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
                    <component :is="Trophy" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Link href="/admin/success">
                    <Button variant="outline" size="sm" class="w-full justify-start">
                      <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour
                    </Button>
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="Trophy" :size="18" />
                  FORMULAIRE DE CRÉATION
                </h3>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <form @submit.prevent="submit" class="space-y-6">
                  <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content">Informations de base</h3>
                    <Input
                      v-model="form.title"
                      label="Nom du badge/succès"
                      placeholder="Ex: Collectionneur débutant"
                      :error="props.errors?.title"
                      required
                      maxlength="100"
                    />
                    <Input
                      v-model="form.description"
                      label="Description"
                      placeholder="Ex: Ajouter 25 Pokémon différents au Pokédex."
                      :error="props.errors?.description"
                      maxlength="250"
                      type="textarea"
                    />
                    <Select
                      v-model="form.type"
                      label="Type de badge/succès"
                      :options="typeOptions"
                      :error="props.errors?.type"
                      required
                      placeholder="Choisir un type"
                    />
                    <Input
                      v-model="form.cash_reward"
                      label="Récompense Cash (₽)"
                      type="number"
                      min="0"
                      :error="props.errors?.cash_reward"
                      placeholder="Ex: 1000"
                    />
                    <Input
                      v-model="form.xp_reward"
                      label="Récompense XP"
                      type="number"
                      min="0"
                      :error="props.errors?.xp_reward"
                      placeholder="Ex: 500"
                    />
                    <div class="space-y-2">
                      <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-1">
                        Conditions de déblocage
                      </label>
                      <div class="flex flex-col w-full gap-3 bg-base-200/60 rounded-lg p-4 border border-base-300/30">
                        <div class="w-full">
                          <Select
                            v-model="newRequirementKey"
                            :options="requirementOptions"
                            size="sm"
                            label="Type de condition"
                            class="w-full"
                          />
                        </div>
                        <div
                          v-if="newRequirementKey === 'count' || newRequirementKey === 'level' || newRequirementKey === 'pokemon_id'"
                          class="w-full"
                        >
                          <Input
                            v-model="newRequirementValue"
                            type="number"
                            label="Valeur"
                            size="sm"
                            class="w-full"
                          />
                        </div>
                        <div v-else-if="newRequirementKey === 'rarity'" class="w-full">
                          <Select
                            v-model="newRequirementValue"
                            :options="rarityOptions"
                            size="sm"
                            label="Rareté"
                            class="w-full"
                          />
                        </div>
                        <div v-else-if="newRequirementKey === 'shiny'" class="w-full">
                          <Select
                            v-model="newRequirementValue"
                            :options="shinyOptions"
                            size="sm"
                            label="Shiny ?"
                            class="w-full"
                          />
                        </div>
                        <div v-else-if="newRequirementKey === 'types'" class="flex flex-col w-full gap-2">
                          <Input
                            v-model="typeKey"
                            placeholder="Type (ex: Feu)"
                            size="sm"
                            class="w-40"
                            maxlength="20"
                          />
                          <Input
                            v-model="typeValue"
                            type="number"
                            placeholder="Quantité"
                            size="sm"
                            class="w-32"
                            min="1"
                          />
                          <Button
                            type="button"
                            size="sm"
                            class="w-fit"
                            :disabled="!typeKey || !typeValue || typesList.some((t) => t.key === typeKey)"
                            @click="addTypeToTypes"
                          >
                            <component :is="Plus" :size="16" />
                          </Button>
                          <div v-if="typesList.length" class="flex flex-wrap gap-2 mt-2">
                            <span
                              v-for="t in typesList"
                              :key="t.key"
                              class="flex items-center gap-1 bg-primary/10 border border-primary/30 px-2 py-1 rounded-full text-xs font-mono hover:bg-primary/20 transition"
                            >
                              {{ t.key }}: {{ t.value }}
                              <Button
                                type="button"
                                size="2xs"
                                variant="error"
                                @click="removeTypeFromTypes(t.key)"
                              >
                                <component :is="X" :size="12" />
                              </Button>
                            </span>
                          </div>
                        </div>
                        <div class="w-full">
                          <Button type="button" size="sm" class="w-full" @click="addRequirement">
                            <component :is="Plus" :size="16" class="mr-2" />
                            Ajouter la condition
                          </Button>
                        </div>
                        <div v-if="requirements.length" class="flex flex-wrap gap-2 mt-2 w-full">
                          <span
                            v-for="req in requirements"
                            :key="req.key"
                            class="flex items-center gap-1 bg-primary/10 border border-primary/30 px-3 py-1 rounded-full text-xs font-mono hover:bg-primary/20 transition"
                          >
                            <span class="font-bold">
                              {{ requirementOptions.find((o) => o.value === req.key)?.label || req.key }}
                            </span>
                            <span v-if="req.key !== 'types'">: {{ req.value }}</span>
                            <span v-else>
                              <span v-for="(v, k) in req.value" :key="k" class="ml-1">
                                {{ k }}:{{ v }}
                              </span>
                            </span>
                            <Button
                              type="button"
                              size="2xs"
                              variant="error"
                              @click="removeRequirement(req.key)"
                            >
                              <component :is="X" :size="12" />
                            </Button>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="space-y-2">
                      <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase flex items-center gap-1">
                        <component :is="FileImage" :size="14" />
                        Image
                      </label>
                      <input
                        type="file"
                        accept="image/*"
                        @change="handleImageChange"
                        class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-content hover:file:bg-primary-focus"
                      />
                      <p v-if="props.errors?.image" class="text-xs text-error">
                        {{ props.errors.image }}
                      </p>
                      <div v-if="imagePreview" class="mt-2">
                        <img
                          :src="imagePreview"
                          alt="Preview"
                          class="w-24 h-24 object-cover rounded-lg border border-base-300/30"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="flex justify-end gap-3 mt-8">
                    <Button variant="ghost" type="button" @click="$inertia.visit('/admin/success')">
                      Annuler
                    </Button>
                    <Button variant="primary" type="submit" :loading="isLoading">
                      <component :is="Trophy" :size="16" class="mr-2" />
                      Créer le succès
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
