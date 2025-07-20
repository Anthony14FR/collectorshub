<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Button from "@/Components/UI/Button.vue";
import Badge from "@/Components/UI/Badge.vue";
import RarityBadge from "@/Components/UI/RarityBadge.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";
import { Zap, ArrowLeft, Edit, Trash2, AlertTriangle, Home, BarChart3, Hash, Package, FileImage, Type, Shield, DollarSign, Calendar, Clock } from 'lucide-vue-next';

const props = defineProps({
  auth: Object,
  item: Object,
});

const getTypeLabel = (type) => {
  const types = {
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

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Zap" :size="28" class="inline align-middle mr-2" />
            DÉTAILS DE L'ITEM
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Informations complètes sur l'item
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
                  <Link :href="`/admin/items/${item.id}/edit`">
                    <Button variant="secondary" size="sm" class="w-full justify-start">
                      <component :is="Edit" :size="16" class="mr-2" /> Modifier
                    </Button>
                  </Link>
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
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="BarChart3" :size="18" />
                    STATISTIQUES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">ID</span>
                    <span class="text-sm font-bold text-info">{{ item.id }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Prix</span>
                    <span class="text-sm font-bold text-primary">{{ item.price }} ₽</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Type</span>
                    <Badge :variant="item.type === 'avatar' || item.type === 'background' ? 'primary' : 'secondary'" class="text-xs">
                      {{ getTypeLabel(item.type) }}
                    </Badge>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Rareté</span>
                    <RarityBadge :rarity="item.rarity" />
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Clock" :size="18" />
                    MÉTADONNÉES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Créé le</span>
                    <span class="text-xs">{{ formatDate(item.created_at) }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Modifié le</span>
                    <span class="text-xs">{{ formatDate(item.updated_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="Package" :size="18" />
                  DÉTAILS DE L'ITEM
                </h3>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <div class="flex flex-col md:flex-row gap-6 mb-8">
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                      <h2 class="text-2xl sm:text-3xl font-bold text-base-content">
                        {{ item.name }}
                      </h2>
                      <Badge :variant="item.type === 'avatar' || item.type === 'background' ? 'primary' : 'secondary'">
                        {{ getTypeLabel(item.type) }}
                      </Badge>
                      <RarityBadge :rarity="item.rarity" />
                    </div>

                    <p v-if="item.description" class="text-base-content/70 text-lg mb-4">
                      {{ item.description }}
                    </p>

                    <div class="flex items-center gap-4 text-2xl font-bold text-primary">
                      <span>{{ item.price }} ₽</span>
                    </div>
                  </div>

                  <div v-if="item.image" class="flex-shrink-0">
                    <div class="w-32 h-32 bg-base-200 rounded-lg border border-base-300/30 flex items-center justify-center overflow-hidden">
                      <img
                        :src="item.image.startsWith('/') ? item.image : '/' + item.image"
                        :alt="item.name"
                        class="w-full h-full object-cover"
                      />
                    </div>
                  </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                  <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300/30 pb-2 flex items-center gap-2">
                      <component :is="Package" :size="18" />
                      INFORMATIONS DE BASE
                    </h3>

                    <div class="space-y-3">
                      <div class="flex justify-between items-center">
                        <span class="font-medium text-base-content/70 flex items-center gap-2">
                          <component :is="Hash" :size="14" />
                          ID:
                        </span>
                        <span class="font-mono text-sm">{{ item.id }}</span>
                      </div>

                      <div class="flex justify-between items-center">
                        <span class="font-medium text-base-content/70 flex items-center gap-2">
                          <component :is="Type" :size="14" />
                          Type:
                        </span>
                        <Badge :variant="item.type === 'avatar' || item.type === 'background' ? 'primary' : 'secondary'">
                          {{ getTypeLabel(item.type) }}
                        </Badge>
                      </div>

                      <div class="flex justify-between items-center">
                        <span class="font-medium text-base-content/70 flex items-center gap-2">
                          <component :is="Shield" :size="14" />
                          Rareté:
                        </span>
                        <RarityBadge :rarity="item.rarity" />
                      </div>

                      <div class="flex justify-between items-center">
                        <span class="font-medium text-base-content/70 flex items-center gap-2">
                          <component :is="DollarSign" :size="14" />
                          Prix:
                        </span>
                        <span class="font-bold text-primary">{{ item.price }} ₽</span>
                      </div>

                      <div class="flex justify-between items-center">
                        <span class="font-medium text-base-content/70 flex items-center gap-2">
                          <component :is="FileImage" :size="14" />
                          Image:
                        </span>
                        <span class="font-mono text-sm">{{ item.image || "Aucune" }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300/30 pb-2 flex items-center gap-2">
                      <component :is="Calendar" :size="18" />
                      MÉTADONNÉES
                    </h3>

                    <div class="space-y-3">
                      <div class="flex justify-between items-center">
                        <span class="font-medium text-base-content/70 flex items-center gap-2">
                          <component :is="Clock" :size="14" />
                          Créé le:
                        </span>
                        <span>{{ formatDate(item.created_at) }}</span>
                      </div>

                      <div class="flex justify-between items-center">
                        <span class="font-medium text-base-content/70 flex items-center gap-2">
                          <component :is="Clock" :size="14" />
                          Modifié le:
                        </span>
                        <span>{{ formatDate(item.updated_at) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                  <Link href="/admin/items">
                    <Button variant="outline">
                      <component :is="ArrowLeft" :size="16" class="mr-2" />
                      Retour à la liste
                    </Button>
                  </Link>
                  <Link :href="`/admin/items/${item.id}/edit`">
                    <Button variant="primary">
                      <component :is="Edit" :size="16" class="mr-2" />
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
  </div>
</template>
