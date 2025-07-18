<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import type { User } from '@/types/user';
import { Head, router } from '@inertiajs/vue3';

const { user } = defineProps<{
  user: User;
}>();

const getRoleColor = (role: string) => {
  switch (role) {
  case 'admin': return 'text-error bg-error/10 border-error/20';
  case 'premium': return 'text-warning bg-warning/10 border-warning/20';
  default: return 'text-info bg-info/10 border-info/20';
  }
};

const getStatusColor = (status: string) => {
  switch (status) {
  case 'active': return 'text-success bg-success/10 border-success/20';
  case 'suspended': return 'text-warning bg-warning/10 border-warning/20';
  case 'banned': return 'text-error bg-error/10 border-error/20';
  default: return 'text-base-content/70 bg-base-200/50 border-base-300/20';
  }
};

const deleteUser = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
    router.delete(`/admin/users/${user.id}`, {
      onSuccess: () => router.visit('/admin/users')
    });
  }
};
</script>

<template>

  <Head :title="`Utilisateur: ${user.username}`" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1
            class="text-2xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-1 tracking-wider">
            👤 PROFIL UTILISATEUR
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Détails de {{ user.username }}
          </p>
        </div>
      </div>

      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">⚙️</span>
              ACTIONS
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button @click="router.visit(`/admin/users/${user.id}/edit`)" variant="secondary" size="sm" class="w-full">
              ✏️ Modifier
            </Button>
            <Button @click="deleteUser" variant="outline" size="sm"
                    class="w-full text-error hover:text-error hover:bg-error/10">
              🗑️ Supprimer
            </Button>
            <div class="border-t border-base-300/30 pt-2">
              <Button @click="router.visit('/admin/users')" variant="outline" size="sm" class="w-full">
                ← Liste utilisateurs
              </Button>
              <Button @click="router.visit('/admin')" variant="ghost" size="sm" class="w-full mt-1">
                🏠 Dashboard
              </Button>
            </div>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">⚡</span>
              STATS RAPIDES
            </h3>
          </div>
          <div class="p-3 space-y-3">
            <div class="text-center">
              <div class="text-2xl font-bold text-warning">{{ user.level || 1 }}</div>
              <div class="text-xs text-base-content/70">Niveau</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-bold text-info">{{ (user.experience || 0).toLocaleString() }}</div>
              <div class="text-xs text-base-content/70">XP</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-bold text-success">{{ (user.cash || 0).toLocaleString() }}</div>
              <div class="text-xs text-base-content/70">Cash 💰</div>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[700px] h-[700px]">
        <div
          class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-6 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
            <div class="flex items-center gap-4">
              <div
                class="w-20 h-20 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-2xl font-bold">
                {{ user.username.charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1">
                <h3 class="text-2xl font-bold text-base-content">{{ user.username }}</h3>
                <p class="text-base-content/60 mb-2">{{ user.email }}</p>
                <div class="flex gap-2">
                  <span :class="[
                    'inline-flex px-3 py-1 text-xs font-semibold rounded-full border',
                    getRoleColor(user.role)
                  ]">
                    {{ user.role }}
                  </span>
                  <span :class="[
                    'inline-flex px-3 py-1 text-xs font-semibold rounded-full border',
                    getStatusColor(user.status)
                  ]">
                    {{ user.status }}
                  </span>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm text-base-content/70">ID</div>
                <div class="text-lg font-bold text-primary">#{{ user.id }}</div>
              </div>
            </div>
          </div>

          <div class="flex-1 overflow-y-auto p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

              <div class="bg-base-200/30 backdrop-blur-sm rounded-xl p-5 border border-base-300/20">
                <h4 class="text-lg font-bold text-base-content mb-4 flex items-center gap-2">
                  📊 Informations générales
                </h4>
                <dl class="space-y-4">
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Nom d'utilisateur</dt>
                    <dd class="text-sm text-base-content font-semibold">{{ user.username }}</dd>
                  </div>
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Email</dt>
                    <dd class="text-sm text-base-content">{{ user.email }}</dd>
                  </div>
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Rôle</dt>
                    <dd>
                      <span :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full border',
                        getRoleColor(user.role)
                      ]">
                        {{ user.role }}
                      </span>
                    </dd>
                  </div>
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Statut</dt>
                    <dd>
                      <span :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full border',
                        getStatusColor(user.status)
                      ]">
                        {{ user.status }}
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>

              <div class="bg-base-200/30 backdrop-blur-sm rounded-xl p-5 border border-base-300/20">
                <h4 class="text-lg font-bold text-base-content mb-4 flex items-center gap-2">
                  ⚡ Progression de jeu
                </h4>
                <dl class="space-y-4">
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Niveau</dt>
                    <dd class="text-xl font-bold text-warning">{{ user.level || 1 }}</dd>
                  </div>
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Expérience</dt>
                    <dd class="text-lg font-semibold text-info">{{ (user.experience || 0).toLocaleString() }} XP</dd>
                  </div>
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Cash</dt>
                    <dd class="text-lg font-bold text-success">{{ (user.cash || 0).toLocaleString() }} 💰</dd>
                  </div>
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Avatar</dt>
                    <dd class="text-sm text-base-content">{{ user.avatar || 'Défaut' }}</dd>
                  </div>
                </dl>
              </div>

              <div class="bg-base-200/30 backdrop-blur-sm rounded-xl p-5 border border-base-300/20">
                <h4 class="text-lg font-bold text-base-content mb-4 flex items-center gap-2">
                  🕒 Activité du compte
                </h4>
                <dl class="space-y-4">
                  <div class="flex justify-between items-start">
                    <dt class="text-sm font-medium text-base-content/70">Inscription</dt>
                    <dd class="text-sm text-base-content text-right">
                      <div>{{ new Date(user.created_at).toLocaleDateString('fr-FR') }}</div>
                      <div class="text-xs text-base-content/60">{{ new Date(user.created_at).toLocaleTimeString('fr-FR')
                      }}</div>
                    </dd>
                  </div>
                  <div class="flex justify-between items-start">
                    <dt class="text-sm font-medium text-base-content/70">Dernière connexion</dt>
                    <dd class="text-sm text-base-content text-right">
                      {{ user.last_login ?
                        new Date(user.last_login).toLocaleDateString('fr-FR') :
                        'Jamais' }}
                      <div v-if="user.last_login" class="text-xs text-base-content/60">
                        {{ new Date(user.last_login).toLocaleTimeString('fr-FR') }}
                      </div>
                    </dd>
                  </div>
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">Email vérifié</dt>
                    <dd>
                      <span :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full border',
                        user.email_verified_at ? 'text-success bg-success/10 border-success/20' : 'text-warning bg-warning/10 border-warning/20'
                      ]">
                        {{ user.email_verified_at ? '✅ Vérifié' : '⚠️ Non vérifié' }}
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>

              <div class="bg-base-200/30 backdrop-blur-sm rounded-xl p-5 border border-base-300/20">
                <h4 class="text-lg font-bold text-base-content mb-4 flex items-center gap-2">
                  🔧 Informations techniques
                </h4>
                <dl class="space-y-4">
                  <div class="flex justify-between items-center">
                    <dt class="text-sm font-medium text-base-content/70">ID utilisateur</dt>
                    <dd class="text-sm font-mono bg-base-300/30 px-2 py-1 rounded border">
                      #{{ user.id }}
                    </dd>
                  </div>
                  <div class="flex justify-between items-start">
                    <dt class="text-sm font-medium text-base-content/70">Créé le</dt>
                    <dd class="text-xs text-base-content/60 text-right font-mono">
                      {{ new Date(user.created_at).toISOString() }}
                    </dd>
                  </div>
                  <div v-if="user.updated_at && user.updated_at !== user.created_at"
                       class="flex justify-between items-start">
                    <dt class="text-sm font-medium text-base-content/70">Modifié le</dt>
                    <dd class="text-xs text-base-content/60 text-right font-mono">
                      {{ new Date(user.updated_at).toISOString() }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>