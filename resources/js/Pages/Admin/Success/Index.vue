<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import Alert from "@/Components/UI/Alert.vue";
import Toast from "@/Components/UI/Toast.vue";
import Badge from "@/Components/UI/Badge.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";
import Modal from "@/Components/UI/Modal.vue";
import { Trophy, Home, Plus, Eye, Edit, Trash2, AlertTriangle, ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';

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

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Trophy" :size="28" class="inline align-middle mr-2" />
            GESTION DES SUCCÈS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Création et gestion des succès/badges
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
                  <Link href="/admin/success/create">
                    <Button variant="secondary" size="sm" class="w-full justify-start mb-2">
                      <component :is="Plus" :size="16" class="mr-2" /> Créer un succès
                    </Button>
                  </Link>
                  <Link href="/admin">
                    <Button variant="outline" size="sm" class="w-full justify-start">
                      <component :is="Home" :size="16" class="mr-2" /> Dashboard
                    </Button>
                  </Link>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Trophy" :size="18" />
                    STATISTIQUES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Total</span>
                    <span class="text-sm font-bold text-info">{{ props.stats?.total || 0 }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Page</span>
                    <span class="text-sm font-bold">{{ props.successes?.current_page || 1 }}/{{ props.successes?.last_page || 1 }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Par page</span>
                    <span class="text-sm font-bold">{{ perPage }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-base-content/70">Afficher :</span>
                    <select
                      :value="perPage"
                      @change="changePerPage(parseInt($event.target.value))"
                      class="bg-base-200/50 border border-base-300/30 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                      <option value="16">16</option>
                      <option value="32">32</option>
                      <option value="64">64</option>
                      <option value="100">100</option>
                    </select>
                    <span class="text-xs text-base-content/50">sur {{ props.successes?.total || 0 }} succès</span>
                  </div>
                  <div class="text-sm text-base-content/70">
                    Page {{ props.successes?.current_page || 1 }} sur {{ props.successes?.last_page || 1 }}
                  </div>
                </div>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <Alert v-if="notification" :type="notification.type" :message="notification.message" />

                <div class="bg-base-200/30 backdrop-blur-sm rounded-xl p-3 mb-4">
                  <div class="flex flex-wrap items-center justify-between gap-2">
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
                        <span class="bg-base-content/20 text-xs px-2 py-1 rounded-full">{{ tab.count }}</span>
                      </button>
                    </div>
                    <Link href="/admin/success/create">
                      <Button variant="primary" size="sm">
                        <component :is="Plus" :size="16" class="mr-2" />
                        Créer
                      </Button>
                    </Link>
                  </div>
                </div>

                <div v-if="hasNoSuccesses" class="text-center py-12">
                  <h3 class="text-xl font-semibold mb-2">Aucun succès trouvé</h3>
                  <p class="text-base-content/70 mb-6">Commencez par créer votre premier succès</p>
                  <Link href="/admin/success/create">
                    <Button variant="primary">
                      <component :is="Plus" :size="20" class="mr-2" />
                      Créer un succès
                    </Button>
                  </Link>
                </div>

                <div v-else class="space-y-4">
                  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                      v-for="success in successesList"
                      :key="success.id"
                      class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-4 hover:border-primary/40 transition-all duration-200 flex flex-col justify-between min-h-[320px] group hover:scale-105"
                    >
                      <div>
                        <div class="flex items-start gap-3 mb-3">
                          <div v-if="success.image_url" class="flex-shrink-0">
                            <div class="w-16 h-16 bg-base-200 rounded-lg border border-base-300/30 flex items-center justify-center overflow-hidden">
                              <img
                                :src="success.image_url"
                                :alt="success.title"
                                class="w-full h-full object-cover"
                              />
                            </div>
                          </div>
                          <div class="flex-1">
                            <h3 class="font-semibold text-lg mb-1 group-hover:text-primary transition-colors">
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
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium">Type:</span>
                          <span class="text-sm font-bold text-primary">{{ success.type }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium">Récompense Cash:</span>
                          <span class="text-xs text-base-content/70">{{ success.cash_reward || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium">Récompense XP:</span>
                          <span class="text-xs text-base-content/70">{{ success.xp_reward || 0 }}</span>
                        </div>
                        <div class="flex gap-2 pt-2">
                          <Link :href="`/admin/success/${success.id}`" class="flex-1">
                            <Button variant="ghost" size="sm" class="w-full" title="Voir le succès">
                              <component :is="Eye" :size="16" />
                            </Button>
                          </Link>
                          <Link :href="`/admin/success/${success.id}/edit`" class="flex-1">
                            <Button variant="secondary" size="sm" class="w-full" title="Modifier le succès">
                              <component :is="Edit" :size="16" />
                            </Button>
                          </Link>
                          <Button
                            variant="error"
                            size="sm"
                            @click="confirmDelete(success)"
                            class="flex-1"
                            title="Supprimer le succès"
                          >
                            <component :is="Trash2" :size="16" />
                          </Button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div v-if="props.successes?.last_page > 1" class="mt-6 flex justify-center">
                    <div class="flex items-center gap-2">
                      <Link
                        v-if="props.successes?.current_page > 1"
                        :href="getPageUrl(1)"
                        class="p-2 bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content rounded-lg transition-all duration-200"
                        title="Première page"
                      >
                        <component :is="ChevronsLeft" :size="16" />
                      </Link>
                      <Link
                        v-if="props.successes?.prev_page_url"
                        :href="props.successes.prev_page_url"
                        class="p-2 bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content rounded-lg transition-all duration-200"
                        title="Page précédente"
                      >
                        <component :is="ChevronLeft" :size="16" />
                      </Link>
                      <div class="flex items-center gap-1">
                        <Link
                          v-for="page in getVisiblePages()"
                          :key="page"
                          :href="getPageUrl(page)"
                          :class="[
                            'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 min-w-[2.5rem] text-center',
                            page === props.successes?.current_page
                              ? 'bg-primary text-primary-content shadow-lg'
                              : 'bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content',
                          ]"
                        >
                          {{ page }}
                        </Link>
                      </div>
                      <Link
                        v-if="props.successes?.next_page_url"
                        :href="props.successes.next_page_url"
                        class="p-2 bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content rounded-lg transition-all duration-200"
                        title="Page suivante"
                      >
                        <component :is="ChevronRight" :size="16" />
                      </Link>
                      <Link
                        v-if="props.successes?.current_page < props.successes?.last_page"
                        :href="getPageUrl(props.successes.last_page)"
                        class="p-2 bg-base-200/50 hover:bg-base-200 text-base-content/70 hover:text-base-content rounded-lg transition-all duration-200"
                        title="Dernière page"
                      >
                        <component :is="ChevronsRight" :size="16" />
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <component :is="AlertTriangle" :size="24" class="text-error" />
          </div>
          <div class="ml-3">
            <h3 class="text-lg font-medium text-base-content">Confirmer la suppression</h3>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-sm text-base-content/70">
            Êtes-vous sûr de vouloir supprimer le succès
            <strong>{{ successToDelete?.name }}</strong>
            ? Cette action est irréversible.
          </p>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <Button variant="ghost" @click="showDeleteModal = false">Annuler</Button>
          <Button variant="error" @click="deleteSuccess" :loading="isLoading">
            <component :is="Trash2" :size="16" class="mr-2" />
            Supprimer
          </Button>
        </div>
      </div>
    </Modal>

    <Toast v-if="showToast" :message="toastMessage" @close="showToast = false" />
  </div>
</template>
