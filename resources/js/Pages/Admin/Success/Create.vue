<script setup>
import { reactive, ref } from "vue";
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
              🏅 CRÉER UN SUCCÈS
            </h1>
            <p
              class="text-xs text-base-content/70 uppercase tracking-wider"
            >
              Ajouter un nouveau succès/badge
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/admin/success">
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
                <div class="space-y-4">
                  <h3
                    class="text-lg font-semibold text-base-content"
                  >
                    Informations de base
                  </h3>
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
                    <label
                      class="block text-xs font-bold tracking-wider text-base-content/80 uppercase"
                    >
                      Requirements (JSON)
                    </label>
                    <textarea
                      v-model="form.requirements"
                      class="textarea textarea-bordered w-full"
                      rows="4"
                      placeholder="Ex: { 'count': 25 }"
                    ></textarea>
                    <p
                      v-if="props.errors?.requirements"
                      class="text-xs text-error"
                    >
                      {{ props.errors.requirements }}
                    </p>
                  </div>
                  <div class="space-y-2">
                    <label
                      class="block text-xs font-bold tracking-wider text-base-content/80 uppercase"
                    >
                      Image
                    </label>
                    <input
                      type="file"
                      accept="image/*"
                      @change="handleImageChange"
                      class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-content hover:file:bg-primary-focus"
                    />
                    <p
                      v-if="props.errors?.image"
                      class="text-xs text-error"
                    >
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
                  <Button
                    variant="ghost"
                    type="button"
                    @click="
                      $inertia.visit('/admin/success')
                    "
                  >
                    Annuler
                  </Button>
                  <Button
                    variant="primary"
                    type="submit"
                    :loading="isLoading"
                  >
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
</template>
