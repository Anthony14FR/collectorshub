<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import Alert from "@/Components/UI/Alert.vue";
import Toast from "@/Components/UI/Toast.vue";
import Badge from "@/Components/UI/Badge.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";
import Modal from "@/Components/UI/Modal.vue";

const props = defineProps({
  auth: Object,
  successes: Object,
  stats: Object,
  filters: Object,
  flash: Object,
});

const showDeleteModal = ref(false);
const successToDelete = ref(null);
const notification = ref(null);
const showToast = ref(false);
const toastMessage = ref("");
const isLoading = ref(false);
const activeTab = ref(props.filters?.type || "all");
const perPage = ref(props.filters?.per_page || 16);

if (props.flash?.success) {
  toastMessage.value = props.flash.success;
  showToast.value = true;
}

if (props.flash?.error) {
  notification.value = {
    type: "error",
    message: props.flash.error,
  };
  setTimeout(() => {
    notification.value = null;
  }, 5000);
}

const successesList = computed(() => {
  return props.successes?.data || [];
});

const hasNoSuccesses = computed(() => {
  return !successesList.value || successesList.value.length === 0;
});

const tabs = computed(() => [
  { id: "all", label: "Tous", count: props.stats?.total || 0 },
  // Ajoute ici d'autres types si besoin
]);

const confirmDelete = (success) => {
  successToDelete.value = success;
  showDeleteModal.value = true;
};

const deleteSuccess = () => {
  if (!successToDelete.value) return;
  isLoading.value = true;
  const params = {};
  if (activeTab.value !== "all") {
    params.type = activeTab.value;
  }
  if (perPage.value !== 16) {
    params.per_page = perPage.value;
  }
  const url = new URL(
    `/admin/success/${successToDelete.value.id}`,
    window.location.origin
  );
  Object.keys(params).forEach((key) => {
    url.searchParams.append(key, params[key]);
  });
  router.delete(url.pathname + url.search, {
    onSuccess: () => {
      showDeleteModal.value = false;
      toastMessage.value = "Le succès a été supprimé avec succès";
      showToast.value = true;
    },
    onError: () => {
      notification.value = {
        type: "error",
        message: "Une erreur est survenue lors de la suppression",
      };
    },
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

const changeTab = (tabId) => {
  activeTab.value = tabId;
  const params = tabId === "all" ? {} : { type: tabId };
  if (perPage.value !== 16) {
    params.per_page = perPage.value;
  }
  router.get("/admin/success", params, {
    preserveState: true,
    replace: true,
  });
};

const changePerPage = (newPerPage) => {
  perPage.value = newPerPage;
  const params = activeTab.value === "all" ? {} : { type: activeTab.value };
  if (newPerPage !== 16) {
    params.per_page = newPerPage;
  }
  router.get("/admin/success", params, {
    preserveState: true,
    replace: true,
  });
};

const getVisiblePages = () => {
  const current = props.successes.current_page;
  const last = props.successes.last_page;
  const maxVisible = 5;
  if (last <= maxVisible) {
    return Array.from({ length: last }, (_, i) => i + 1);
  }
  let start = Math.max(current - 2, 1);
  let end = Math.min(start + maxVisible - 1, last);
  if (end - start + 1 < maxVisible) {
    start = Math.max(end - maxVisible + 1, 1);
  }
  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
};

const getPageUrl = (page) => {
  const url = new URL(window.location.href);
  url.searchParams.set("page", page);
  return url.pathname + url.search;
};
</script>

<template>
  <Head title="Gestion des Succès" />
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
              GESTION DES SUCCÈS
            </h1>
            <p
              class="text-xs text-base-content/70 uppercase tracking-wider"
            >
              Création et gestion des succès/badges
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
            <Link href="/admin/success/create">
              <Button variant="primary" size="sm">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                  />
                </svg>
                Créer
              </Button>
            </Link>
          </div>
        </div>
      </div>
      <div class="flex-1 overflow-hidden">
        <div class="h-full overflow-y-auto p-4">
          <div class="max-w-7xl mx-auto">
            <Alert
              v-if="notification"
              :type="notification.type"
              :message="notification.message"
            />
            <div class="space-y-4">
              <div
                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
              >
                <div class="flex items-center gap-2">
                  <span
                    class="text-sm font-medium text-base-content/70"
                  >Afficher :</span
                  >
                  <select
                    :value="perPage"
                    @change="
                      changePerPage(
                        parseInt($event.target.value)
                      )
                    "
                    class="bg-base-200/50 border border-base-300/30 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                  >
                    <option value="16">16</option>
                    <option value="32">32</option>
                    <option value="64">64</option>
                    <option value="100">100</option>
                  </select>
                  <span class="text-xs text-base-content/50"
                  >sur {{ successes.total }} succès</span
                  >
                </div>
                <div class="text-sm text-base-content/70">
                  Page {{ successes.current_page }} sur
                  {{ successes.last_page }}
                </div>
              </div>
              <div
                class="bg-base-100/30 backdrop-blur-md rounded-lg border border-base-300/30 p-2"
              >
                <div
                  class="flex flex-wrap items-center justify-between gap-2"
                >
                  <div class="flex flex-wrap gap-2">
                    <button
                      v-for="tab in tabs"
                      :key="tab.id"
                      @click="changeTab(tab.id)"
                      :class="[
                        'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                        activeTab === tab.id
                          ? 'bg-primary text-primary-content shadow-lg'
                          : 'bg-base-200/50 text-base-content/70 hover:bg-base-200 hover:text-base-content',
                      ]"
                    >
                      <span>{{ tab.label }}</span>
                      <span
                        class="bg-base-content/20 text-xs px-2 py-1 rounded-full"
                      >{{ tab.count }}</span
                      >
                    </button>
                  </div>
                  <Link href="/admin/success/create">
                    <Button variant="primary" size="sm">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        />
                      </svg>
                      Créer
                    </Button>
                  </Link>
                </div>
              </div>
              <div
                v-if="hasNoSuccesses"
                class="text-center py-12"
              >
                <h3 class="text-xl font-semibold mb-2">
                  Aucun succès trouvé
                </h3>
                <p class="text-base-content/70 mb-6">
                  Commencez par créer votre premier succès
                </p>
                <Link href="/admin/success/create">
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
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                      />
                    </svg>
                    Créer un succès
                  </Button>
                </Link>
              </div>
              <div v-else class="space-y-4">
                <div
                  class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                  <div
                    v-for="success in successesList"
                    :key="success.id"
                    class="bg-base-100/50 backdrop-blur-md rounded-lg border border-base-300/30 p-4 hover:shadow-lg transition-all duration-200 flex flex-col justify-between min-h-[320px]"
                  >
                    <div>
                      <div
                        class="flex items-start gap-3 mb-3"
                      >
                        <div
                          v-if="success.image_url"
                          class="flex-shrink-0"
                        >
                          <div
                            class="w-16 h-16 bg-base-200 rounded-lg border border-base-300/30 flex items-center justify-center overflow-hidden"
                          >
                            <img
                              :src="success.image_url"
                              :alt="success.title"
                              class="w-full h-full object-cover"
                            />
                          </div>
                        </div>
                        <div class="flex-1">
                          <h3
                            class="font-semibold text-lg mb-1"
                          >
                            {{ success.title }}
                          </h3>
                          <p
                            v-if="success.description"
                            class="text-sm text-base-content/70 mb-2 line-clamp-3 max-h-[4.5em] overflow-hidden"
                          >
                            {{ success.description }}
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="space-y-2 mt-auto">
                      <div
                        class="flex justify-between items-center"
                      >
                        <span
                          class="text-sm font-medium"
                        >Type:</span
                        >
                        <span
                          class="text-sm font-bold text-primary"
                        >{{ success.type }}</span
                        >
                      </div>
                      <div
                        class="flex justify-between items-center"
                      >
                        <span
                          class="text-sm font-medium"
                        >Récompense Cash:</span
                        >
                        <span
                          class="text-xs text-base-content/70"
                        >{{
                          success.cash_reward || 0
                        }}</span
                        >
                      </div>
                      <div
                        class="flex justify-between items-center"
                      >
                        <span
                          class="text-sm font-medium"
                        >Récompense XP:</span
                        >
                        <span
                          class="text-xs text-base-content/70"
                        >{{
                          success.xp_reward || 0
                        }}</span
                        >
                      </div>
                      <div class="flex gap-2 pt-2">
                        <Link
                          :href="`/admin/success/${success.id}`"
                          class="flex-1"
                        >
                          <Button
                            variant="ghost"
                            size="sm"
                            class="w-full"
                            title="Voir le succès"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                              />
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                              />
                            </svg>
                          </Button>
                        </Link>
                        <Link
                          :href="`/admin/success/${success.id}/edit`"
                          class="flex-1"
                        >
                          <Button
                            variant="secondary"
                            size="sm"
                            class="w-full"
                            title="Modifier le succès"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4"
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
                          </Button>
                        </Link>
                        <Button
                          variant="error"
                          size="sm"
                          @click="confirmDelete(success)"
                          class="flex-1"
                          title="Supprimer le succès"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                          </svg>
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  v-if="successes.last_page > 1"
                  class="mt-6 flex justify-center"
                >
                  <div class="flex items-center gap-2">
                    <Link
                      v-if="successes.current_page > 1"
                      :href="getPageUrl(1)"
                      class="p-2 bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content rounded-lg transition-all duration-200"
                      title="Première page"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                        />
                      </svg>
                    </Link>
                    <Link
                      v-if="successes.prev_page_url"
                      :href="successes.prev_page_url"
                      class="p-2 bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content rounded-lg transition-all duration-200"
                      title="Page précédente"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M15 19l-7-7 7-7"
                        />
                      </svg>
                    </Link>
                    <div class="flex items-center gap-1">
                      <Link
                        v-for="page in getVisiblePages()"
                        :key="page"
                        :href="getPageUrl(page)"
                        :class="[
                          'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 min-w-[2.5rem] text-center',
                          page ===
                            successes.current_page
                            ? 'bg-primary text-primary-content shadow-lg'
                            : 'bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content',
                        ]"
                      >
                        {{ page }}
                      </Link>
                    </div>
                    <Link
                      v-if="successes.next_page_url"
                      :href="successes.next_page_url"
                      class="p-2 bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content rounded-lg transition-all duration-200"
                      title="Page suivante"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </Link>
                    <Link
                      v-if="
                        successes.current_page <
                          successes.last_page
                      "
                      :href="
                        getPageUrl(successes.last_page)
                      "
                      class="p-2 bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content rounded-lg transition-all duration-200"
                      title="Dernière page"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M13 5l7 7-7 7m-8 0l7-7-7-7"
                        />
                      </svg>
                    </Link>
                  </div>
                </div>
              </div>
            </div>
            <Modal
              :show="showDeleteModal"
              @close="showDeleteModal = false"
            >
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <div class="flex-shrink-0">
                    <svg
                      class="h-6 w-6 text-error"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                      />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h3
                      class="text-lg font-medium text-base-content"
                    >
                      Confirmer la suppression
                    </h3>
                  </div>
                </div>
                <div class="mt-2">
                  <p class="text-sm text-base-content/70">
                    Êtes-vous sûr de vouloir supprimer le
                    succès
                    <strong>{{
                      successToDelete?.name
                    }}</strong>
                    ? Cette action est irréversible.
                  </p>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                  <Button
                    variant="ghost"
                    @click="showDeleteModal = false"
                  >Annuler</Button
                  >
                  <Button
                    variant="error"
                    @click="deleteSuccess"
                    :loading="isLoading"
                  >Supprimer</Button
                  >
                </div>
              </div>
            </Modal>
            <Toast
              v-if="showToast"
              :message="toastMessage"
              @close="showToast = false"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
