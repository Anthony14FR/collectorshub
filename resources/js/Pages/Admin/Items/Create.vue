<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import Input from "@/Components/UI/Input.vue";
import Select from "@/Components/UI/Select.vue";
import Alert from "@/Components/UI/Alert.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";

const props = defineProps({
  auth: Object,
  errors: Object,
});

const form = ref({
  name: "",
  description: "",
  type: "heal",
  effect: {},
  price: 0,
  rarity: "normal",
  image: "",
});

const isLoading = ref(false);

const typeOptions = [
  { value: "heal", label: "Soin" },
  { value: "boost", label: "Boost" },
  { value: "evolution", label: "Évolution" },
  { value: "special", label: "Spécial" },
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

const statOptions = [
  { value: "hp", label: "PV" },
  { value: "attack", label: "Attaque" },
  { value: "defense", label: "Défense" },
  { value: "speed", label: "Vitesse" },
  { value: "special_attack", label: "Attaque Spé." },
  { value: "special_defense", label: "Défense Spé." },
];

const specialTypeOptions = [
  { value: "shiny", label: "Shiny" },
  { value: "rare", label: "Rare" },
  { value: "legendary", label: "Légendaire" },
];

const effectFields = computed(() => {
  switch (form.value.type) {
  case "heal":
    return [
      {
        name: "amount",
        label: "Montant de soin",
        type: "number",
        min: 1,
        required: true,
      },
    ];
  case "boost":
    return [
      {
        name: "stat",
        label: "Statistique",
        type: "select",
        options: statOptions,
        required: true,
      },
      {
        name: "amount",
        label: "Montant du boost",
        type: "number",
        min: 1,
        max: 32,
        required: true,
      },
    ];
  case "evolution":
    return [
      {
        name: "pokemon_id",
        label: "ID du Pokémon",
        type: "number",
        min: 1,
        required: true,
      },
    ];
  case "special":
    return [
      {
        name: "type",
        label: "Type d'effet",
        type: "select",
        options: specialTypeOptions,
        required: true,
      },
    ];
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

  router.post("/admin/items", form.value, {
    onSuccess: () => {
      // Redirection gérée par le contrôleur
    },
    onError: () => {
      isLoading.value = false;
    },
  });
};
</script>

<template>
  <Head title="Créer un Item" />

  <div
    class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative"
  >
    <BackgroundEffects />

    <div
      class="relative z-10 h-screen w-screen overflow-hidden flex flex-col"
    >
      <div
        class="shrink-0 p-4 bg-base-200/50 backdrop-blur-md border-b border-base-300/30"
      >
        <div
          class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4"
        >
          <div>
            <h1
              class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider"
            >
              🛍️ CRÉER UN ITEM
            </h1>
            <p
              class="text-xs text-base-content/70 uppercase tracking-wider"
            >
              Ajouter un nouvel item au shop
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/admin">
              <Button variant="ghost" size="sm">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-1"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                  />
                </svg>
                Dashboard
              </Button>
            </Link>
            <Link href="/admin/items">
              <Button variant="ghost" size="sm">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-1"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                  />
                </svg>
                Retour
              </Button>
            </Link>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-hidden">
        <div class="h-full overflow-y-auto p-4">
          <div class="max-w-2xl mx-auto">
            <div
              class="bg-base-100/50 backdrop-blur-md rounded-lg border border-base-300/30 p-6"
            >
              <form @submit.prevent="submit" class="space-y-6">
                <!-- Informations de base -->
                <div class="space-y-4">
                  <h3
                    class="text-lg font-semibold text-base-content"
                  >
                    Informations de base
                  </h3>

                  <Input
                    v-model="form.name"
                    label="Nom de l'item"
                    placeholder="Ex: Potion de soin"
                    :error="errors?.name"
                    required
                    maxlength="50"
                  />

                  <Input
                    v-model="form.description"
                    label="Description"
                    placeholder="Description de l'item (optionnel)"
                    :error="errors?.description"
                    maxlength="250"
                    type="textarea"
                  />

                  <div class="grid grid-cols-2 gap-4">
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

                  <Input
                    v-model="form.image"
                    label="Image (chemin)"
                    placeholder="Ex: /images/items/potion.png"
                    :error="errors?.image"
                  />
                </div>

                <!-- Effets -->
                <div
                  v-if="effectFields.length > 0"
                  class="space-y-4"
                >
                  <h3
                    class="text-lg font-semibold text-base-content"
                  >
                    Effets
                  </h3>

                  <div
                    v-for="field in effectFields"
                    :key="field.name"
                    class="space-y-2"
                  >
                    <label
                      class="block text-sm font-medium text-base-content"
                    >
                      {{ field.label }}
                      <span
                        v-if="field.required"
                        class="text-error"
                      >*</span
                      >
                    </label>

                    <Select
                      v-if="field.type === 'select'"
                      :model-value="
                        form.effect[field.name]
                      "
                      :options="field.options"
                      :error="
                        errors?.[`effect.${field.name}`]
                      "
                      @update:model-value="
                        updateEffect(field.name, $event)
                      "
                      :required="field.required"
                    />

                    <Input
                      v-else
                      :model-value="
                        form.effect[field.name]
                      "
                      :type="field.type"
                      :placeholder="field.label"
                      :error="
                        errors?.[`effect.${field.name}`]
                      "
                      @update:model-value="
                        updateEffect(field.name, $event)
                      "
                      :required="field.required"
                      :min="field.min"
                      :max="field.max"
                    />
                  </div>
                </div>

                <!-- Actions -->
                <div
                  class="flex justify-end gap-3 pt-6 border-t border-base-300/30"
                >
                  <Link href="/admin/items">
                    <Button variant="ghost" type="button">
                      Annuler
                    </Button>
                  </Link>
                  <Button
                    variant="primary"
                    type="submit"
                    :loading="isLoading"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                      />
                    </svg>
                    Créer l'item
                  </Button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
