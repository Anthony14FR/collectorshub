<script setup lang="ts">
import type { User } from "@/types/user";
import { Head, Link, router } from "@inertiajs/vue3";
import { reactive, ref } from "vue";

interface Props {
  auth: {
    user: User;
  };
}

const { auth } = defineProps<Props>();

const unlockedAvatars = Array.isArray(auth.user.unlocked_avatars)
  ? auth.user.unlocked_avatars
  : JSON.parse(auth.user.unlocked_avatars);

console.log(
  "Nombre d'avatars débloqués :",
  unlockedAvatars.length,
  unlockedAvatars
);
const profileForm = reactive({
  username: auth.user.username,
  email: auth.user.email,
  processing: false,
  errors: {} as Record<string, string>,
});

const passwordForm = reactive({
  current_password: "",
  password: "",
  password_confirmation: "",
  processing: false,
  errors: {} as Record<string, string>,
});

const avatarForm = reactive({
  avatar: auth.user.avatar,
  processing: false,
  errors: {} as Record<string, string>,
});

const showProfileForm = ref(false);
const showPasswordForm = ref(false);

const updateProfile = () => {
  profileForm.processing = true;
  profileForm.errors = {};

  router.patch(
    "/profile",
    {
      username: profileForm.username,
      email: profileForm.email,
    },
    {
      onSuccess: () => {
        profileForm.processing = false;
        showProfileForm.value = false;
      },
      onError: (errors: Record<string, string>) => {
        profileForm.processing = false;
        profileForm.errors = errors;
      },
    }
  );
};

const updatePassword = () => {
  passwordForm.processing = true;
  passwordForm.errors = {};

  router.patch(
    "/password",
    {
      current_password: passwordForm.current_password,
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation,
    },
    {
      onSuccess: () => {
        passwordForm.processing = false;
        passwordForm.current_password = "";
        passwordForm.password = "";
        passwordForm.password_confirmation = "";
        showPasswordForm.value = false;
      },
      onError: (errors: Record<string, string>) => {
        passwordForm.processing = false;
        passwordForm.errors = errors;
      },
    }
  );
};

const updateAvatar = () => {
  avatarForm.processing = true;
  avatarForm.errors = {};
  router.patch(
    "/avatar",
    { avatar: avatarForm.avatar },
    {
      onSuccess: () => {
        avatarForm.processing = false;
      },
      onError: (errors: Record<string, string>) => {
        avatarForm.processing = false;
        avatarForm.errors = errors;
      },
    }
  );
};

const cancelProfileEdit = () => {
  profileForm.username = auth.user.username;
  profileForm.email = auth.user.email;
  profileForm.errors = {};
  showProfileForm.value = false;
};

const cancelPasswordEdit = () => {
  passwordForm.current_password = "";
  passwordForm.password = "";
  passwordForm.password_confirmation = "";
  passwordForm.errors = {};
  showPasswordForm.value = false;
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString("fr-FR", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

const getUserInitial = () => {
  return auth.user?.username?.charAt(0)?.toUpperCase() || "?";
};
</script>

<template>
  <Head title="Mon Profil" />

  <div
    class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900"
  >
    <header class="relative">
      <div
        class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"
      ></div>

      <nav
        class="relative z-10 flex items-center justify-between p-6 lg:px-8"
      >
        <div class="flex lg:flex-1">
          <Link
            href="/"
            class="text-2xl font-bold text-white hover:text-blue-300 transition-colors"
          >
            CollectorsHub
          </Link>
        </div>

        <div class="flex items-center space-x-4">
          <Link
            href="/me"
            class="text-white text-sm hover:text-blue-300 transition-colors"
          >
            ← Retour au profil
          </Link>
        </div>
      </nav>
    </header>

    <main class="relative z-10 mx-auto max-w-4xl px-6 py-12">
      <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
          <img
            :src="
              auth.user.avatar
                ? auth.user.avatar
                : '/images/trainer/1.png'
            "
            alt="Avatar utilisateur"
            class="w-24 h-24 rounded-full border-4 border-blue-500 bg-gray-800 object-cover"
            style="image-rendering: pixelated"
          />
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">
          Éditer mon profil
        </h1>
        <p class="text-gray-400">
          Modifiez vos informations personnelles
        </p>
      </div>

      <div
        class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10 mb-8"
      >
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-white">
            Informations personnelles
          </h2>
          <button
            @click="showProfileForm = !showProfileForm"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
          >
            {{ showProfileForm ? "Annuler" : "Modifier" }}
          </button>
        </div>

        <div v-if="!showProfileForm" class="grid md:grid-cols-2 gap-6">
          <div>
            <label
              class="block text-sm font-medium text-gray-300 mb-1"
            >
              Nom d'utilisateur
            </label>
            <div
              class="bg-gray-800/50 border border-gray-600 rounded-md p-3 text-white"
            >
              {{ auth.user.username }}
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-300 mb-1"
            >
              Adresse email
            </label>
            <div
              class="bg-gray-800/50 border border-gray-600 rounded-md p-3 text-white"
            >
              {{ auth.user.email }}
              <span
                v-if="auth.user.email_verified_at"
                class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-900/50 text-green-300 border border-green-500"
              >
                ✓ Vérifiée
              </span>
            </div>
          </div>
        </div>

        <form
          v-if="showProfileForm"
          @submit.prevent="updateProfile"
          class="space-y-6"
        >
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label
                for="username"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Nom d'utilisateur
              </label>
              <input
                id="username"
                v-model="profileForm.username"
                type="text"
                required
                class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                placeholder="Votre nom d'utilisateur"
              />
              <div
                v-if="profileForm.errors.username"
                class="mt-2 text-sm text-red-400"
              >
                {{ profileForm.errors.username }}
              </div>
            </div>

            <div>
              <label
                for="email"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Adresse email
              </label>
              <input
                id="email"
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                placeholder="votre@email.com"
              />
              <div
                v-if="profileForm.errors.email"
                class="mt-2 text-sm text-red-400"
              >
                {{ profileForm.errors.email }}
              </div>
            </div>
          </div>

          <div class="flex space-x-4">
            <button
              type="submit"
              :disabled="profileForm.processing"
              class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="profileForm.processing"
              >Enregistrement...</span
              >
              <span v-else>Enregistrer</span>
            </button>
            <button
              type="button"
              @click="cancelProfileEdit"
              class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
            >
              Annuler
            </button>
          </div>
        </form>
      </div>

      <div
        class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10 mb-8"
      >
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-white">
            Mot de passe
          </h2>
          <button
            @click="showPasswordForm = !showPasswordForm"
            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors"
          >
            {{
              showPasswordForm
                ? "Annuler"
                : "Changer le mot de passe"
            }}
          </button>
        </div>

        <div v-if="!showPasswordForm">
          <p class="text-gray-400">
            Votre mot de passe est sécurisé et chiffré. Cliquez sur
            "Changer le mot de passe" pour le modifier.
          </p>
        </div>

        <form
          v-if="showPasswordForm"
          @submit.prevent="updatePassword"
          class="space-y-6"
        >
          <div>
            <label
              for="current_password"
              class="block text-sm font-medium text-gray-300 mb-2"
            >
              Mot de passe actuel
            </label>
            <input
              id="current_password"
              v-model="passwordForm.current_password"
              type="password"
              required
              class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors"
              placeholder="Votre mot de passe actuel"
            />
            <div
              v-if="passwordForm.errors.current_password"
              class="mt-2 text-sm text-red-400"
            >
              {{ passwordForm.errors.current_password }}
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label
                for="password"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Nouveau mot de passe
              </label>
              <input
                id="password"
                v-model="passwordForm.password"
                type="password"
                required
                class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors"
                placeholder="Nouveau mot de passe"
              />
              <div
                v-if="passwordForm.errors.password"
                class="mt-2 text-sm text-red-400"
              >
                {{ passwordForm.errors.password }}
              </div>
            </div>

            <div>
              <label
                for="password_confirmation"
                class="block text-sm font-medium text-gray-300 mb-2"
              >
                Confirmer le nouveau mot de passe
              </label>
              <input
                id="password_confirmation"
                v-model="passwordForm.password_confirmation"
                type="password"
                required
                class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors"
                placeholder="Confirmer le mot de passe"
              />
              <div
                v-if="passwordForm.errors.password_confirmation"
                class="mt-2 text-sm text-red-400"
              >
                {{ passwordForm.errors.password_confirmation }}
              </div>
            </div>
          </div>

          <div class="flex space-x-4">
            <button
              type="submit"
              :disabled="passwordForm.processing"
              class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="passwordForm.processing"
              >Mise à jour...</span
              >
              <span v-else>Mettre à jour le mot de passe</span>
            </button>
            <button
              type="button"
              @click="cancelPasswordEdit"
              class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
            >
              Annuler
            </button>
          </div>
        </form>
      </div>

      <div
        class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10 mb-8"
      >
        <h2 class="text-xl font-semibold text-white mb-6">
          Changer d'avatar
        </h2>
        <form @submit.prevent="updateAvatar">
          <div class="flex space-x-4 mb-4 overflow-x-auto max-w-full">
            <label
              v-for="avatar in unlockedAvatars"
              :key="avatar"
              class="cursor-pointer flex flex-col items-center p-2"
              style="min-width: 72px;"
            >
              <input
                type="radio"
                v-model="avatarForm.avatar"
                :value="avatar"
                class="hidden"
              />
              <img
                :src="avatar"
                :alt="'Avatar'"
                class="w-14 h-14 rounded-full border-4 transition-all duration-150"
                :class="avatarForm.avatar === avatar
                  ? 'border-blue-500 scale-110'
                  : 'border-gray-600 opacity-70 hover:opacity-100'"
              />
              <span
                v-if="avatarForm.avatar === avatar"
                class="text-xs text-blue-400 font-bold mt-1"
              >Choisi</span
              >
            </label>
          </div>
          <button
            type="submit"
            :disabled="avatarForm.processing"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
          >
            Enregistrer l'avatar
          </button>
          <div
            v-if="avatarForm.errors.avatar"
            class="mt-2 text-sm text-red-400"
          >
            {{ avatarForm.errors.avatar }}
          </div>
        </form>
      </div>

      <div
        class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10"
      >
        <h2 class="text-xl font-semibold text-white mb-6">
          Informations du compte
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
          <div>
            <label
              class="block text-sm font-medium text-gray-300 mb-1"
            >
              Niveau
            </label>
            <div
              class="bg-gray-800/50 border border-gray-600 rounded-md p-3 text-white"
            >
              {{ auth.user.level }}
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-300 mb-1"
            >
              Expérience
            </label>
            <div
              class="bg-gray-800/50 border border-gray-600 rounded-md p-3 text-white"
            >
              {{ auth.user.experience }} XP
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-300 mb-1"
            >
              Cash
            </label>
            <div
              class="bg-gray-800/50 border border-gray-600 rounded-md p-3 text-white"
            >
              {{ auth.user.cash }} $
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-300 mb-1"
            >
              Rôle
            </label>
            <div
              class="bg-gray-800/50 border border-gray-600 rounded-md p-3 text-white"
            >
              {{ auth.user.role }}
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-300 mb-1"
            >
              Membre depuis
            </label>
            <div
              class="bg-gray-800/50 border border-gray-600 rounded-md p-3 text-white"
            >
              {{ formatDate(auth.user.created_at) }}
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-300 mb-1"
            >
              Statut
            </label>
            <div
              class="bg-gray-800/50 border border-gray-600 rounded-md p-3"
            >
              <span
                :class="{
                  'bg-green-900/50 text-green-300 border-green-500':
                    auth.user.status === 'active',
                  'bg-yellow-900/50 text-yellow-300 border-yellow-500':
                    auth.user.status === 'suspended',
                  'bg-red-900/50 text-red-300 border-red-500':
                    auth.user.status === 'banned',
                }"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm border"
              >
                {{
                  auth.user.status === "active"
                    ? "🟢 Actif"
                    : auth.user.status === "suspended"
                      ? "🟡 Suspendu"
                      : "🔴 Banni"
                }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
