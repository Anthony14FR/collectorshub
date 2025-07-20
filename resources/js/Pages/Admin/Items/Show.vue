<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import Badge from "@/Components/UI/Badge.vue";
import RarityBadge from "@/Components/UI/RarityBadge.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";

const props = defineProps({
  auth: Object,
  item: Object,
});

const getTypeLabel = (type) => {
  const types = {
    heal: "Soin",
    boost: "Boost",
    evolution: "Évolution",
    special: "Spécial",
    ball: "Ball",
    avatar: "Avatar",
    background: "Background",
  };
  return types[type] || type;
};

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};
</script>

<template>
  <Head :title="`Item: ${item.name}`" />

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
              👁️ DÉTAILS DE L'ITEM
            </h1>
            <p
              class="text-xs text-base-content/70 uppercase tracking-wider"
            >
              Informations complètes sur l'item
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
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-hidden">
        <div class="h-full overflow-y-auto p-4">
          <div class="max-w-4xl mx-auto">
            <div
              class="bg-base-100/50 backdrop-blur-md rounded-lg border border-base-300/30 p-6"
            >
              <!-- En-tête de l'item -->
              <div class="flex flex-col md:flex-row gap-6 mb-8">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-4">
                    <h2
                      class="text-3xl font-bold text-base-content"
                    >
                      {{ item.name }}
                    </h2>
                    <Badge
                      :variant="
                        item.type === 'avatar' ||
                          item.type === 'background'
                          ? 'primary'
                          : 'secondary'
                      "
                    >
                      {{ getTypeLabel(item.type) }}
                    </Badge>
                    <RarityBadge :rarity="item.rarity" />
                  </div>

                  <p
                    v-if="item.description"
                    class="text-base-content/70 text-lg mb-4"
                  >
                    {{ item.description }}
                  </p>

                  <div
                    class="flex items-center gap-4 text-2xl font-bold text-primary"
                  >
                    <span>{{ item.price }} ₽</span>
                  </div>
                </div>

                <div v-if="item.image" class="flex-shrink-0">
                  <div
                    class="w-32 h-32 bg-base-200 rounded-lg border border-base-300/30 flex items-center justify-center overflow-hidden"
                  >
                    <img
                      :src="
                        item.image.startsWith('/')
                          ? item.image
                          : '/' + item.image
                      "
                      :alt="item.name"
                      class="w-full h-full object-cover"
                    />
                  </div>
                </div>
              </div>

              <!-- Informations détaillées -->
              <div class="grid md:grid-cols-2 gap-8">
                <!-- Informations de base -->
                <div class="space-y-4">
                  <h3
                    class="text-xl font-semibold text-base-content border-b border-base-300/30 pb-2"
                  >
                    Informations de base
                  </h3>

                  <div class="space-y-3">
                    <div
                      class="flex justify-between items-center"
                    >
                      <span
                        class="font-medium text-base-content/70"
                      >ID:</span
                      >
                      <span class="font-mono text-sm">{{
                        item.id
                      }}</span>
                    </div>

                    <div
                      class="flex justify-between items-center"
                    >
                      <span
                        class="font-medium text-base-content/70"
                      >Type:</span
                      >
                      <Badge
                        :variant="
                          item.type === 'avatar' ||
                            item.type === 'background'
                            ? 'primary'
                            : 'secondary'
                        "
                      >
                        {{ getTypeLabel(item.type) }}
                      </Badge>
                    </div>

                    <div
                      class="flex justify-between items-center"
                    >
                      <span
                        class="font-medium text-base-content/70"
                      >Rareté:</span
                      >
                      <RarityBadge
                        :rarity="item.rarity"
                      />
                    </div>

                    <div
                      class="flex justify-between items-center"
                    >
                      <span
                        class="font-medium text-base-content/70"
                      >Prix:</span
                      >
                      <span class="font-bold text-primary"
                      >{{ item.price }} ₽</span
                      >
                    </div>

                    <div
                      class="flex justify-between items-center"
                    >
                      <span
                        class="font-medium text-base-content/70"
                      >Image:</span
                      >
                      <span class="font-mono text-sm">{{
                        item.image || "Aucune"
                      }}</span>
                    </div>
                  </div>
                </div>

                <!-- Métadonnées -->
                <div class="space-y-4">
                  <h3
                    class="text-xl font-semibold text-base-content border-b border-base-300/30 pb-2"
                  >
                    Métadonnées
                  </h3>

                  <div class="space-y-3">
                    <div
                      class="flex justify-between items-center"
                    >
                      <span
                        class="font-medium text-base-content/70"
                      >Créé le:</span
                      >
                      <span>{{
                        formatDate(item.created_at)
                      }}</span>
                    </div>

                    <div
                      class="flex justify-between items-center"
                    >
                      <span
                        class="font-medium text-base-content/70"
                      >Modifié le:</span
                      >
                      <span>{{
                        formatDate(item.updated_at)
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="mt-8 flex justify-end gap-3">
                <Link href="/admin/items">
                  <Button variant="ghost">
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
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                      />
                    </svg>
                    Retour à la liste
                  </Button>
                </Link>
                <Link :href="`/admin/items/${item.id}/edit`">
                  <Button variant="primary">
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
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                    Modifier cet item
                  </Button>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
