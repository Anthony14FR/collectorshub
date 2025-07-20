<script setup>
import { reactive, ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import Input from "@/Components/UI/Input.vue";
import Select from "@/Components/UI/Select.vue";
import Alert from "@/Components/UI/Alert.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";

const props = defineProps({
  success: Object,
  errors: Object,
});

const form = reactive({
  title: props.success.title || "",
  description: props.success.description || "",
  type: props.success.type || "",
  requirements: JSON.stringify(props.success.requirements || {}, null, 2),
  cash_reward: props.success.cash_reward ?? 0,
  xp_reward: props.success.xp_reward ?? 0,
  image: null,
});

const isLoading = ref(false);
const imagePreview = ref(props.success.image_url || null);

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
    imagePreview.value = props.success.image_url || null;
  }
};

watch(
  () => props.success.image_url,
  (newVal) => {
    if (!form.image) {
      imagePreview.value = newVal || null;
    }
  }
);

const submit = () => {
  isLoading.value = true;
  const formData = new FormData();
  formData.append("title", form.title);
  formData.append("description", form.description);
  formData.append("type", form.type);
  if (form.image) {
    formData.append("image", form.image);
  }
  try {
    formData.append(
      "requirements",
      JSON.stringify(JSON.parse(form.requirements))
    );
  } catch (e) {
    formData.append("requirements", form.requirements);
  }
  formData.append("cash_reward", form.cash_reward);
  formData.append("xp_reward", form.xp_reward);
  formData.append("_method", "put");
  router.post(`/admin/success/${props.success.id}`, formData, {
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
  <Head title="Modifier le Succès" />
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
              🏅 MODIFIER LE SUCCÈS
            </h1>
            <p
              class="text-xs text-base-content/70 uppercase tracking-wider"
            >
              Modifier un succès/badge
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
                    :model-value="form.title"
                    @update:model-value="
                      (val) => (form.title = val)
                    "
                    label="Nom du badge/succès"
                    placeholder="Ex: Collectionneur d'avatars"
                    :error="props.errors?.title"
                    required
                    maxlength="100"
                  />
                  <Input
                    :model-value="form.description"
                    @update:model-value="
                      (val) => (form.description = val)
                    "
                    label="Description"
                    placeholder="Ex: Débloque ce badge en collectionnant 10 avatars."
                    :error="props.errors?.description"
                    maxlength="250"
                    type="textarea"
                  />
                  <Select
                    :model-value="form.type"
                    @update:model-value="
                      (val) => (form.type = val)
                    "
                    label="Type de badge/succès"
                    :options="typeOptions"
                    :error="props.errors?.type"
                    required
                  />
                  <Input
                    :model-value="form.cash_reward"
                    @update:model-value="
                      (val) => (form.cash_reward = val)
                    "
                    label="Récompense Cash (₽)"
                    type="number"
                    min="0"
                    :error="props.errors?.cash_reward"
                  />
                  <Input
                    :model-value="form.xp_reward"
                    @update:model-value="
                      (val) => (form.xp_reward = val)
                    "
                    label="Récompense XP"
                    type="number"
                    min="0"
                    :error="props.errors?.xp_reward"
                  />
                  <div class="space-y-2">
                    <label
                      class="block text-xs font-bold tracking-wider text-base-content/80 uppercase"
                    >
                      Image
                    </label>
                    <div
                      v-if="props.success.image_url"
                      class="mb-2"
                    >
                      <img
                        :src="props.success.image_url"
                        alt="Image actuelle"
                        class="w-24 h-24 object-cover rounded-lg border border-base-300/30"
                      />
                    </div>
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
                  </div>
                  <div class="space-y-2">
                    <Input
                      v-model="form.requirements"
                      label="Requirements (JSON)"
                      type="textarea"
                      rows="4"
                      placeholder="Ex: { 'count': 25 }"
                      :error="props.errors?.requirements"
                    />
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
                    Enregistrer
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
