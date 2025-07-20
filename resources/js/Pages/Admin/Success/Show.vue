<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";

const props = defineProps({
  success: Object,
});

function formatDate(dateStr) {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return (
    d.toLocaleDateString("fr-FR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    }) +
    " " +
    d.toLocaleTimeString("fr-FR", { hour: "2-digit", minute: "2-digit" })
  );
}
</script>

<template>
  <Head :title="`Succès: ${success.title}`" />
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
          class="max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4"
        >
          <div>
            <h1
              class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider"
            >
              👁️ DÉTAILS DU SUCCÈS
            </h1>
            <p
              class="text-xs text-base-content/70 uppercase tracking-wider"
            >
              Informations complètes sur le succès/badge
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/admin/success">
              <Button variant="ghost" size="sm">
                Retour à la liste
              </Button>
            </Link>
            <Link :href="`/admin/success/${success.id}/edit`">
              <Button variant="secondary" size="sm">
                Modifier
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
              <div class="flex flex-col md:flex-row gap-6 mb-8">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-4">
                    <h2
                      class="text-3xl font-bold text-base-content"
                    >
                      {{ success.title }}
                    </h2>
                    <span
                      class="px-3 py-1 rounded-lg bg-primary/10 text-primary text-xs font-bold"
                    >{{ success.type }}</span
                    >
                  </div>
                  <p
                    v-if="success.description"
                    class="text-base-content/70 text-lg mb-4"
                  >
                    {{ success.description }}
                  </p>
                  <div
                    class="flex flex-col gap-2 text-base-content/80 text-sm mb-4"
                  >
                    <div>
                      <span class="font-bold"
                      >Récompense Cash :</span
                      >
                      <span>{{
                        success.cash_reward
                      }}</span>
                    </div>
                    <div>
                      <span class="font-bold"
                      >Récompense XP :</span
                      >
                      <span>{{ success.xp_reward }}</span>
                    </div>
                    <div>
                      <span class="font-bold"
                      >Requirements :</span
                      >
                      <span>
                        <pre
                          class="bg-base-200 rounded p-2 text-xs whitespace-pre-wrap"
                        >{{
                                                        JSON.stringify(
                                                            success.requirements,
                                                            null,
                                                            2
                                                        )
                        }}</pre
                        >
                      </span>
                    </div>
                  </div>
                </div>
                <div
                  v-if="success.image_url"
                  class="flex-shrink-0"
                >
                  <div
                    class="w-32 h-32 bg-base-200 rounded-lg border border-base-300/30 flex items-center justify-center overflow-hidden"
                  >
                    <img
                      :src="success.image_url"
                      :alt="success.title"
                      class="w-full h-full object-cover"
                    />
                  </div>
                </div>
              </div>
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
                      formatDate(success.created_at)
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
                      formatDate(success.updated_at)
                    }}</span>
                  </div>
                </div>
              </div>
              <div class="mt-8 flex justify-end gap-3">
                <Link href="/admin/success">
                  <Button variant="ghost"
                  >Retour à la liste</Button
                  >
                </Link>
                <Link
                  :href="`/admin/success/${success.id}/edit`"
                >
                  <Button variant="primary"
                  >Modifier ce succès</Button
                  >
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
