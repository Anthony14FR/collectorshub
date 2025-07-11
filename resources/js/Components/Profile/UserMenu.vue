<script setup lang="ts">
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import type { User } from '@/types/user';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
  user: User;
}

const { user } = defineProps<Props>();
const isModalOpen = ref(false);

const logout = () => {
  router.post("/logout");
};
</script>

<template>
  <div class="relative">
    <Button variant="secondary" icon="lightning" size="md" @click="isModalOpen = true" class="w-full">
      MENU
    </Button>

    <Modal :show="isModalOpen" @close="isModalOpen = false">
      <template #header>
        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-gradient-to-br from-secondary/20 to-accent/20 rounded-lg flex items-center justify-center">
            <span class="text-lg">⚡</span>
          </div>
          <h3
            class="text-xl font-bold bg-gradient-to-r from-secondary to-accent bg-clip-text text-transparent">
            Menu Principal
          </h3>
        </div>
      </template>
      <template #default>
        <div class="space-y-6">
          <div
            class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-4 border border-primary/20">
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-full flex items-center justify-center">
                <img
                  :src="
                    user.avatar
                      ? user.avatar
                      : '/images/trainer/1.png'
                  "
                  :alt="user.username"
                  class="w-12 h-12 rounded-full object-cover"
                />
              </div>
              <div>
                <h4 class="font-bold text-lg">
                  {{ user.username }}
                </h4>
                <p class="text-sm opacity-70">
                  {{ user.email }}
                </p>
                <div class="flex items-center gap-2 mt-1">
                  <span
                    class="text-xs bg-gradient-to-r from-primary/20 to-secondary/20 px-2 py-1 rounded-full">
                    Niveau {{ user.level || 1 }}
                  </span>
                  <span
                    class="text-xs bg-gradient-to-r from-accent/20 to-success/20 px-2 py-1 rounded-full">
                    {{ user.cash || 0 }} 💰
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-3">
            <Link
              href="/profile"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary/10 transition-all duration-300 group"
            >
              <div
                class="w-8 h-8 bg-primary/20 rounded-lg flex items-center justify-center group-hover:bg-primary/30 transition-colors"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  ></path>
                </svg>
              </div>
              <span class="font-medium">Mon Profil</span>
            </Link>

            <Link
              href="/collection"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-secondary/10 transition-all duration-300 group"
            >
              <div
                class="w-8 h-8 bg-secondary/20 rounded-lg flex items-center justify-center group-hover:bg-secondary/30 transition-colors"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                  ></path>
                </svg>
              </div>
              <span class="font-medium">Collection</span>
            </Link>

            <Link href="/promocodes"
                  class="flex items-center gap-3 p-3 rounded-xl hover:bg-secondary/10 transition-all duration-300 group">
              <div
                class="w-8 h-8 bg-secondary/20 rounded-lg flex items-center justify-center group-hover:bg-secondary/30 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                  </path>
                </svg>
              </div>
              <span class="font-medium">Codes Promo</span>
            </Link>
                        
                        
            <Link
              href="/gacha"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent/10 transition-all duration-300 group"
            >
              <div
                class="w-8 h-8 bg-accent/20 rounded-lg flex items-center justify-center group-hover:bg-accent/30 transition-colors"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"
                  ></path>
                </svg>
              </div>
              <span class="font-medium">Gacha</span>
            </Link>

            <button
              @click="logout"
              class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-error/10 transition-all duration-300 group text-left"
            >
              <div class="w-8 h-8 bg-error/20 rounded-lg flex items-center justify-center group-hover:bg-error/30 transition-colors">
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                  ></path>
                </svg>
              </div>
              <span class="font-medium">Déconnexion</span>
            </button>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>
