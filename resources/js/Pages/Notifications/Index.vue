<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import CPBadge from '@/Components/UI/CPBadge.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import type { PageProps } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Notification {
  id: number;
  type: string;
  title: string;
  message: string;
  data?: any;
  is_read: boolean;
  created_at: string;
}

interface Props extends PageProps {
  announcements: Notification[];
  marketplace_history: Notification[];
  unread_count: number;
}

const { announcements, marketplace_history, unread_count } = defineProps<Props>();

const activeTab = ref<'announcements' | 'history'>('announcements');

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatPrice = (price: number): string => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' ₽';
};

const getPokemonImageUrl = (pokemonId: number): string => {
  return `/images/pokemon-gifs/${pokemonId}.gif`;
};

const getPokemonStars = (notification: Notification): number => {
  return notification.data?.stars || 1;
};

const getPokemonRarity = (notification: Notification): string => {
  return notification.data?.rarity || 'normal';
};

const getPokemonTypes = (notification: Notification): string[] => {
  return notification.data?.types || ['Normal'];
};

const getPokemonCP = (notification: Notification): number => {
  return notification.data?.cp || 100;
};

const markAsRead = async (notificationId: number) => {
  try {
    const response = await fetch(`/notifications/${notificationId}/read`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (window as any).Laravel?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });

    if (response.ok) {
      router.reload({ only: ['announcements', 'marketplace_history', 'unread_count'] });
    }
  } catch (error) {
    console.error('Erreur lors du marquage comme lu:', error);
  }
};

const markAllAsRead = async () => {
  try {
    const response = await fetch('/notifications/read-all', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (window as any).Laravel?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });

    if (response.ok) {
      router.reload({ only: ['announcements', 'marketplace_history', 'unread_count'] });
    }
  } catch (error) {
    console.error('Erreur lors du marquage comme lu:', error);
  }
};

const getNotificationIcon = (type: string) => {
  switch (type) {
  case 'announcement':
    return '📢';
  case 'marketplace_buy':
    return '🛒';
  case 'marketplace_sell':
    return '💰';
  default:
    return '📝';
  }
};

const getNotificationBg = (type: string, isRead: boolean) => {
  const opacity = isRead ? '10' : '20';
  switch (type) {
  case 'announcement':
    return `bg-primary/${opacity} border-primary/30`;
  case 'marketplace_buy':
  case 'marketplace_sell':
    return `bg-base-100/${opacity} border-base-300/30`;
  default:
    return `bg-base-200/${opacity} border-base-300/30`;
  }
};

const getTransactionTagClass = (type: string) => {
  switch (type) {
  case 'marketplace_buy':
    return 'bg-blue-500/20 text-blue-600 border-blue-500/30';
  case 'marketplace_sell':
    return 'bg-green-500/20 text-green-600 border-green-500/30';
  default:
    return 'bg-gray-500/20 text-gray-600 border-gray-500/30';
  }
};

const getTransactionTagText = (type: string) => {
  switch (type) {
  case 'marketplace_buy':
    return 'Achat';
  case 'marketplace_sell':
    return 'Vente';
  default:
    return 'Transaction';
  }
};

const goBack = () => {
  router.visit('/me');
};
</script>

<template>

  <Head title="Boîte de réception" />

  <div class="min-h-screen">
    <BackgroundEffects />

    <div class="relative z-50 min-h-screen">
      <div class="bg-base-100/80 backdrop-blur-sm border-b border-base-300/20 p-4">
        <div class="flex items-center justify-between max-w-6xl mx-auto">
          <div class="flex items-center gap-4">
            <Button variant="ghost" size="sm" @click="goBack" class="lg:hidden">
              ← Retour
            </Button>
            <div class="flex items-center gap-3">
              <div
                class="w-8 h-8 bg-gradient-to-br from-info/50 to-primary/50 rounded-lg flex items-center justify-center text-lg">
                📬</div>
              <div>
                <h1
                  class="text-xl font-bold bg-gradient-to-r from-info to-primary bg-clip-text text-transparent">
                  Boîte de réception
                </h1>
                <p class="text-sm text-base-content/70">Gérez vos notifications</p>
              </div>
              <span v-if="unread_count > 0"
                    class="bg-error text-error-content text-xs px-2 py-1 rounded-full font-bold">
                {{ unread_count }}
              </span>
            </div>
          </div>
          <div class="hidden lg:flex items-center gap-2">
            <Button variant="outline" size="sm" @click="markAllAsRead" v-if="unread_count > 0">
              Tout marquer comme lu
            </Button>
            <Button variant="ghost" size="sm" @click="goBack">
              ← Retour au profil
            </Button>
          </div>
        </div>
      </div>

      <div class="max-w-6xl mx-auto p-4">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/20 p-6 mb-6">
          <div class="flex bg-base-200/50 rounded-lg p-1 mb-6">
            <button @click="activeTab = 'announcements'" :class="[
              'flex-1 py-3 px-4 rounded-md font-medium transition-all duration-200',
              activeTab === 'announcements'
                ? 'bg-primary/20 text-primary border border-primary/30 shadow-sm'
                : 'text-base-content/70 hover:text-base-content hover:bg-base-200/70'
            ]">
              <div class="flex items-center justify-center gap-2">
                <span>📢</span>
                <span>Annonces</span>
                <span v-if="announcements.filter(n => !n.is_read).length > 0"
                      class="bg-primary/20 text-primary text-xs px-2 py-1 rounded-full font-bold">
                  {{announcements.filter(n => !n.is_read).length}}
                </span>
              </div>
            </button>
            <button @click="activeTab = 'history'" :class="[
              'flex-1 py-3 px-4 rounded-md font-medium transition-all duration-200',
              activeTab === 'history'
                ? 'bg-primary/20 text-primary border border-primary/30 shadow-sm'
                : 'text-base-content/70 hover:text-base-content hover:bg-base-200/70'
            ]">
              <div class="flex items-center justify-center gap-2">
                <span>📜</span>
                <span>Historique</span>
                <span v-if="marketplace_history.filter(n => !n.is_read).length > 0"
                      class="bg-primary/20 text-primary text-xs px-2 py-1 rounded-full font-bold">
                  {{marketplace_history.filter(n => !n.is_read).length}}
                </span>
              </div>
            </button>
          </div>

          <div class="space-y-6">
            <div v-if="activeTab === 'announcements'">
              <div v-if="announcements.length === 0" class="text-center py-12 text-base-content/60">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-lg font-medium mb-2">Aucune annonce</h3>
                <p class="text-sm">Vous n'avez reçu aucune annonce pour le moment.</p>
              </div>
              <div class="space-y-6">
                <div v-for="notification in announcements" :key="notification.id" :class="[
                  'p-4 rounded-lg border cursor-pointer transition-all duration-200 hover:scale-[1.01] hover:shadow-md',
                  getNotificationBg(notification.type, notification.is_read),
                  !notification.is_read ? 'shadow-sm ring-2 ring-primary/20' : ''
                ]" @click="!notification.is_read && markAsRead(notification.id)">
                  <div class="flex items-start gap-4">
                    <span class="text-2xl">{{ getNotificationIcon(notification.type) }}</span>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-3 mb-2">
                        <h4
                          :class="['text-base font-medium', !notification.is_read ? 'font-bold' : '']">
                          {{ notification.title }}
                        </h4>
                        <span v-if="!notification.is_read"
                              class="w-3 h-3 bg-primary rounded-full animate-pulse"></span>
                      </div>
                      <p class="text-sm text-base-content/80 break-words mb-2">{{
                        notification.message
                      }}</p>
                      <span class="text-xs text-base-content/60">{{
                        formatDate(notification.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="activeTab === 'history'">
              <div v-if="marketplace_history.length === 0" class="text-center py-12 text-base-content/60">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-lg font-medium mb-2">Aucun historique</h3>
                <p class="text-sm">Votre historique de transactions sera affiché ici.</p>
              </div>

              <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <div v-for="notification in marketplace_history" :key="notification.id">
                  <div v-if="notification.data" :class="[
                    'bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border transition-all duration-200 group cursor-pointer',
                    !notification.is_read
                      ? 'border-primary/40 ring-2 ring-primary/20'
                      : 'border-base-300/20 hover:border-primary/40'
                  ]" @click="!notification.is_read && markAsRead(notification.id)">

                    <div class="flex flex-col h-full">
                      <div class="flex flex-col items-center gap-4 mb-3">
                        <!-- Top badges -->
                        <div class="flex w-full justify-between items-center">
                          <CPBadge :cp="getPokemonCP(notification)" size="xs"
                                   :show-label="false" />
                          <div class="flex items-center gap-2">
                            <StarsBadge :stars="getPokemonStars(notification)" size="sm" />
                            <div v-if="notification.data.is_shiny"
                                 class="w-5 h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                              <span class="text-yellow-400 text-xs">✨</span>
                            </div>
                            <div v-if="!notification.is_read"
                                 class="w-3 h-3 bg-primary rounded-full animate-pulse"></div>
                          </div>
                        </div>

                        <!-- Pokemon image -->
                        <div class="relative flex-shrink-0 flex items-center">
                          <img :src="getPokemonImageUrl(notification.data.pokemon_id)"
                               :alt="notification.data.pokemon_name"
                               class="w-32 h-32 object-contain group-hover:scale-110 transition-transform duration-300"
                               style="image-rendering: pixelated;" />
                        </div>

                        <!-- Pokemon details -->
                        <div class="flex-1 w-full flex flex-col">
                          <div class="flex justify-between">
                            <div>
                              <div class="flex items-start gap-1">
                                <h4
                                  class="font-bold text-base text-base-content flex items-center gap-1 mt-2">
                                  {{ notification.data.pokemon_name }}
                                  <RarityBadge
                                    :rarity="getPokemonRarity(notification)"
                                    size="xs" />
                                </h4>
                              </div>
                            </div>
                          </div>

                          <div class="mb-2">
                            <div class="text-lg font-bold text-warning">{{
                              formatPrice(notification.data.price) }}</div>
                          </div>

                          <div class="flex flex-wrap gap-1">
                            <PokemonTypeBadge
                              v-for="(type, index) in getPokemonTypes(notification)"
                              :key="index" :type="type" size="xxs" />
                          </div>
                        </div>
                      </div>

                      <!-- Bottom section -->
                      <div
                        class="flex items-center justify-between mt-auto pt-2 border-t border-base-300/20">
                        <div class="text-xs text-base-content/70">
                          <span
                            v-if="notification.data.seller_name && notification.type === 'marketplace_buy'">
                            Vendeur : {{ notification.data.seller_name }}
                          </span>
                          <span
                            v-if="notification.data.buyer_name && notification.type === 'marketplace_sell'">
                            Acheteur : {{ notification.data.buyer_name }}
                          </span>
                          <div class="text-xs text-base-content/50 mt-1">
                            {{ formatDate(notification.created_at) }}
                          </div>
                        </div>

                        <div>
                          <span :class="[
                            'px-3 py-1 rounded text-xs font-medium',
                            getTransactionTagClass(notification.type)
                          ]">
                            {{ getTransactionTagText(notification.type) }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:hidden bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/20 p-4">
        <div class="space-y-2">
          <Button variant="outline" size="md" @click="markAllAsRead" v-if="unread_count > 0" class="w-full">
            Tout marquer comme lu
          </Button>
          <Button variant="primary" size="md" @click="goBack" class="w-full">
            Retour au profil
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>