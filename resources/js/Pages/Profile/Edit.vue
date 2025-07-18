<script setup lang="ts">
import Alert from '@/Components/UI/Alert.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Modal from '@/Components/UI/Modal.vue';
import type { PageProps } from '@/types';
import type { User } from '@/types/user';
import { Head, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

interface Props extends PageProps {
  auth: {
    user: User;
  };
  mustVerifyEmail?: boolean;
  status?: string;
  success?: string;
}

const props = defineProps<Props>();

const unlockedAvatars = Array.isArray(props.auth.user.unlocked_avatars)
  ? props.auth.user.unlocked_avatars
  : props.auth.user.unlocked_avatars
    ? JSON.parse(props.auth.user.unlocked_avatars)
    : [];

const unlockedBackgrounds = Array.isArray(props.auth.user.unlocked_backgrounds)
  ? props.auth.user.unlocked_backgrounds
  : props.auth.user.unlocked_backgrounds
    ? JSON.parse(props.auth.user.unlocked_backgrounds)
    : [];

const allAvailableBackgrounds = computed(() => {
  const defaultBg = '/images/section-me-background.jpg';
  const otherBackgrounds = unlockedBackgrounds.filter((bg: string) => bg !== defaultBg);
  const result = [defaultBg, ...otherBackgrounds];
  return result;
});

const profileForm = reactive({
  username: props.auth.user.username,
  email: props.auth.user.email,
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
  avatar: props.auth.user.avatar,
  processing: false,
  errors: {} as Record<string, string>,
});

const backgroundForm = reactive({
  background: props.auth.user.background,
  processing: false,
  errors: {} as Record<string, string>,
});

const totpForm = reactive({
  enabled: props.auth.user.totp_enabled || false,
  processing: false,
  errors: {} as Record<string, string>,
});

const updateTotpState = () => {
  totpForm.enabled = props.auth.user.totp_enabled || false;
};

updateTotpState();


const showProfileModal = ref(false);
const showPasswordModal = ref(false);
const showAvatarModal = ref(false);
const showBackgroundModal = ref(false);
const showAlert = ref(false);
const alertType = ref<'success' | 'error'>('success');
const alertMessage = ref('');

const formattedJoinDate = computed(() => {
  return new Date(props.auth.user.created_at).toLocaleDateString("fr-FR", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});


const updateProfile = () => {
  profileForm.processing = true;
  profileForm.errors = {};

  router.patch("/profile", {
    username: profileForm.username,
    email: profileForm.email,
  }, {
    onSuccess: () => {
      profileForm.processing = false;
      showProfileModal.value = false;
      showSuccessAlert("Profil mis à jour avec succès !");
    },
    onError: (errors: Record<string, string>) => {
      profileForm.processing = false;
      profileForm.errors = errors;
    },
  });
};

const updatePassword = () => {
  passwordForm.processing = true;
  passwordForm.errors = {};

  router.put("/password", {
    current_password: passwordForm.current_password,
    password: passwordForm.password,
    password_confirmation: passwordForm.password_confirmation,
  }, {
    onSuccess: () => {
      passwordForm.processing = false;
      passwordForm.current_password = "";
      passwordForm.password = "";
      passwordForm.password_confirmation = "";
      showPasswordModal.value = false;
      showSuccessAlert("Mot de passe mis à jour avec succès !");
    },
    onError: (errors: Record<string, string>) => {
      passwordForm.processing = false;
      passwordForm.errors = errors;
    },
  });
};

const updateAvatar = () => {
  avatarForm.processing = true;
  avatarForm.errors = {};

  router.patch("/avatar", {
    avatar: avatarForm.avatar
  }, {
    onSuccess: () => {
      avatarForm.processing = false;
      showAvatarModal.value = false;
      showSuccessAlert("Avatar mis à jour avec succès !");
    },
    onError: (errors: Record<string, string>) => {
      avatarForm.processing = false;
      avatarForm.errors = errors;
    },
  });
};

const updateBackground = () => {
  backgroundForm.processing = true;
  backgroundForm.errors = {};

  router.patch("/background", {
    background: backgroundForm.background
  }, {
    onSuccess: () => {
      backgroundForm.processing = false;
      showBackgroundModal.value = false;
      showSuccessAlert("Background mis à jour avec succès !");
    },
    onError: (errors: Record<string, string>) => {
      backgroundForm.processing = false;
      backgroundForm.errors = errors;
    },
  });
};

const selectBackground = (backgroundPath: string) => {
  backgroundForm.background = backgroundPath;
};

const cancelProfileEdit = () => {
  profileForm.username = props.auth.user.username;
  profileForm.email = props.auth.user.email;
  profileForm.errors = {};
  showProfileModal.value = false;
};

const cancelPasswordEdit = () => {
  passwordForm.current_password = "";
  passwordForm.password = "";
  passwordForm.password_confirmation = "";
  passwordForm.errors = {};
  showPasswordModal.value = false;
};

const selectAvatar = (avatarPath: string) => {
  avatarForm.avatar = avatarPath;
};

const showSuccessAlert = (message: string) => {
  alertType.value = 'success';
  alertMessage.value = message;
  showAlert.value = true;
  setTimeout(() => {
    showAlert.value = false;
  }, 4000);
};

if (props.success) {
  showSuccessAlert(props.success);
}

const showErrorAlert = (message: string) => {
  alertType.value = 'error';
  alertMessage.value = message;
  showAlert.value = true;
  setTimeout(() => {
    showAlert.value = false;
  }, 4000);
};

const goToProfile = () => {
  router.visit('/me');
};

const toggleTotp = () => {
  if (totpForm.enabled) {
    disableTotp();
  } else {
    enableTotp();
  }
};

const disableTotp = () => {
  totpForm.processing = true;

  router.post('/profile/totp/disable', {}, {
    onSuccess: () => {
      totpForm.processing = false;
      updateTotpState();
    },
    onError: (errors: Record<string, string>) => {
      totpForm.errors = errors;
      totpForm.processing = false;
      showErrorAlert('Erreur lors de la désactivation du TOTP');
    }
  });
};

const enableTotp = () => {
  router.visit('/profile/totp');
};
</script>

<template>

  <Head title="Éditer mon profil" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-y-auto">
      <div
        class="lg:hidden flex items-center justify-between p-4 bg-base-100/60 backdrop-blur-sm border-b border-base-300/30">
        <Button @click="goToProfile" variant="outline" size="sm">
          ← Retour
        </Button>
        <h1 class="text-lg font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
          Mon Profil
        </h1>
        <div class="w-20"></div>
      </div>

      <div class="hidden lg:flex justify-center pt-8 mb-8">
        <div class="text-center">
          <h1
            class="text-3xl font-bold bg-gradient-to-r from-primary via-secondary to-accent bg-clip-text text-transparent mb-2 tracking-wider">
            ⚙️ PROFIL
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            MODIFIER VOS INFORMATIONS
          </p>
        </div>
      </div>

      <div class="absolute top-20 lg:top-32 left-1/2 -translate-x-1/2 w-[400px] z-30">
        <div v-if="showAlert" class="mb-2">
          <Alert :type="alertType" :message="alertMessage" dismissible @dismiss="showAlert = false" />
        </div>
      </div>

      <div class="flex justify-center p-4 lg:p-8">
        <div class="w-full max-w-4xl">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-6">
            <div class="p-4 lg:p-6 bg-gradient-to-r from-primary/10 to-secondary/10 border-b border-primary/20">
              <div class="flex items-center gap-4">
                <div class="relative">
                  <img :src="auth.user.avatar || '/images/trainer/1.png'" :alt="auth.user.username"
                       class="w-16 h-16 lg:w-20 lg:h-20 rounded-full border-4 border-primary/30 bg-base-200 object-cover"
                       style="image-rendering: pixelated" />
                  <button @click="showAvatarModal = true"
                          class="absolute -bottom-1 -right-1 w-6 h-6 lg:w-8 lg:h-8 bg-primary text-primary-content rounded-full flex items-center justify-center hover:bg-primary/80 transition-colors">
                    <svg class="w-3 h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                  </button>
                </div>
                <div class="flex-1">
                  <h2 class="text-xl lg:text-2xl font-bold">{{ auth.user.username }}</h2>
                  <p class="text-sm text-base-content/70">{{ auth.user.email }}</p>
                  <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs bg-gradient-to-r from-primary/20 to-secondary/20 px-3 py-1 rounded-full">
                      Niveau {{ auth.user.level || 1 }}
                    </span>
                    <span class="text-xs bg-gradient-to-r from-accent/20 to-success/20 px-3 py-1 rounded-full">
                      {{ auth.user.cash || 0 }} 💰
                    </span>
                  </div>
                </div>
                <Button @click="showBackgroundModal = true" variant="secondary">
                  🎨 Changer le fond
                </Button>
              </div>
            </div>

            <div class="p-4 lg:p-6 space-y-6">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                      <span class="text-xl">👤</span>
                      Informations personnelles
                    </h3>
                    <Button @click="showProfileModal = true" variant="secondary" size="sm">
                      Modifier
                    </Button>
                  </div>
                  <div class="space-y-3">
                    <div>
                      <label class="text-sm font-medium text-base-content/70">Nom d'utilisateur</label>
                      <div class="mt-1 p-3 bg-base-200/50 rounded-lg">{{ auth.user.username }}</div>
                    </div>
                    <div>
                      <label class="text-sm font-medium text-base-content/70">Email</label>
                      <div class="mt-1 p-3 bg-base-200/50 rounded-lg">{{ auth.user.email }}</div>
                    </div>
                  </div>
                </div>

                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="text-lg font-semibold flex items-center gap-2">
                        <span class="text-xl">🔐</span>
                        Sécurité
                      </h3>
                      <p class="text-sm text-base-content/60 mt-1">
                        Gérez la sécurité de votre compte et l'authentification à deux facteurs
                      </p>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div
                      class="flex items-center justify-between p-4 bg-base-200/30 rounded-lg border border-base-300/30">
                      <div>
                        <label class="text-sm font-medium text-base-content/70">Mot de passe</label>
                        <div class="mt-1 text-base-content">••••••••</div>
                      </div>
                      <Button @click="showPasswordModal = true" variant="secondary" size="sm">
                        Changer
                      </Button>
                    </div>

                    <div
                      class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-base-200/30 rounded-lg border border-base-300/30 gap-3">
                      <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2 sm:mb-0">
                          <label class="text-sm font-medium text-base-content/70">
                            Authentification à deux facteurs
                          </label>
                          <Badge :variant="totpForm.enabled ? 'success' : 'ghost'" size="sm">
                            {{ totpForm.enabled ? 'Activé' : 'Désactivé' }}
                          </Badge>
                        </div>
                        <p class="text-xs text-base-content/50">
                          {{ totpForm.enabled
                            ? 'Votre compte est protégé par un authentificateur'
                            : 'Ajoutez une couche de sécurité supplémentaire avec un authentificateur'
                          }}
                        </p>
                      </div>
                      <Button @click="toggleTotp" :variant="totpForm.enabled ? 'secondary' : 'primary'" size="sm"
                              :disabled="totpForm.processing" class="w-full sm:w-auto">
                        <span v-if="totpForm.processing" class="loading loading-spinner loading-xs"></span>
                        {{ totpForm.enabled ? 'Désactiver' : 'Configurer' }}
                      </Button>
                    </div>

                    <div
                      class="flex items-center justify-between p-4 bg-base-200/30 rounded-lg border border-base-300/30">
                      <div>
                        <label class="text-sm font-medium text-base-content/70">Membre depuis</label>
                        <div class="mt-1 text-base-content">{{ formattedJoinDate }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-6 border-t border-base-300/30">
                <div class="text-center p-4 bg-gradient-to-br from-primary/10 to-primary/5 rounded-xl">
                  <div class="text-2xl font-bold text-primary">{{ auth.user.level || 1 }}</div>
                  <div class="text-sm text-base-content/70">Niveau</div>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-xl">
                  <div class="text-2xl font-bold text-secondary">{{ auth.user.xp || 0 }}</div>
                  <div class="text-sm text-base-content/70">Expérience</div>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-accent/10 to-accent/5 rounded-xl">
                  <div class="text-2xl font-bold text-accent">{{ auth.user.cash || 0 }}</div>
                  <div class="text-sm text-base-content/70">Cash</div>
                </div>
                <div class="text-center p-4 bg-gradient-to-br from-success/10 to-success/5 rounded-xl">
                  <div class="text-2xl font-bold text-success">{{ unlockedAvatars.length }}</div>
                  <div class="text-sm text-base-content/70">Avatars</div>
                </div>
              </div>
            </div>
          </div>

          <div class="text-center">
            <Button @click="goToProfile" variant="secondary" size="lg" class="min-w-48">
              ← Retour à la page d'accueil
            </Button>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showProfileModal" @close="cancelProfileEdit">
      <template #header>
        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center">
            <span class="text-lg">👤</span>
          </div>
          <h3 class="text-xl font-bold">Modifier le profil</h3>
        </div>
      </template>
      <template #default>
        <form @submit.prevent="updateProfile" class="space-y-6">
          <div>
            <Input v-model="profileForm.username" label="Nom d'utilisateur" type="text" required
                   :error="profileForm.errors.username" />
          </div>
          <div>
            <Input v-model="profileForm.email" label="Email" type="email" required :error="profileForm.errors.email" />
          </div>
          <div class="flex gap-3 justify-end">
            <Button @click="cancelProfileEdit" variant="outline" type="button">
              Annuler
            </Button>
            <Button type="submit" :disabled="profileForm.processing">
              {{ profileForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
            </Button>
          </div>
        </form>
      </template>
    </Modal>

    <Modal :show="showPasswordModal" @close="cancelPasswordEdit">
      <template #header>
        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg flex items-center justify-center">
            <span class="text-lg">🔐</span>
          </div>
          <h3 class="text-xl font-bold">Changer le mot de passe</h3>
        </div>
      </template>
      <template #default>
        <form @submit.prevent="updatePassword" class="space-y-6">
          <div>
            <Input v-model="passwordForm.current_password" label="Mot de passe actuel" type="password" required
                   :error="passwordForm.errors.current_password" />
          </div>
          <div>
            <Input v-model="passwordForm.password" label="Nouveau mot de passe" type="password" required
                   :error="passwordForm.errors.password" />
          </div>
          <div>
            <Input v-model="passwordForm.password_confirmation" label="Confirmer le nouveau mot de passe"
                   type="password" required />
          </div>
          <div class="flex gap-3 justify-end">
            <Button @click="cancelPasswordEdit" variant="outline" type="button">
              Annuler
            </Button>
            <Button type="submit" :disabled="passwordForm.processing">
              {{ passwordForm.processing ? 'Mise à jour...' : 'Mettre à jour' }}
            </Button>
          </div>
        </form>
      </template>
    </Modal>

    <Modal :show="showAvatarModal" @close="showAvatarModal = false">
      <template #header>
        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-gradient-to-br from-accent/20 to-primary/20 rounded-lg flex items-center justify-center">
            <span class="text-lg">🎭</span>
          </div>
          <h3 class="text-xl font-bold">Choisir un avatar</h3>
        </div>
      </template>
      <template #default>
        <div class="space-y-6">
          <div class="grid grid-cols-4 lg:grid-cols-6 gap-4">
            <div v-for="avatar in unlockedAvatars" :key="avatar" @click="selectAvatar(avatar)" :class="[
              'relative cursor-pointer rounded-xl border-3 transition-all duration-200 overflow-hidden',
              avatarForm.avatar === avatar
                ? 'border-primary shadow-lg shadow-primary/30 scale-105'
                : 'border-base-300/30 hover:border-primary/50 hover:scale-105'
            ]">
              <img :src="avatar" alt="Avatar" class="w-full h-full object-cover aspect-square"
                   style="image-rendering: pixelated" />
              <div v-if="avatarForm.avatar === avatar"
                   class="absolute inset-0 bg-primary/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
          <div class="flex gap-3 justify-end">
            <Button @click="showAvatarModal = false" variant="outline" type="button">
              Annuler
            </Button>
            <Button @click="updateAvatar" :disabled="avatarForm.processing">
              {{ avatarForm.processing ? 'Mise à jour...' : 'Confirmer' }}
            </Button>
          </div>
        </div>
      </template>
    </Modal>

    <Modal :show="showBackgroundModal" @close="showBackgroundModal = false" max-width="4xl">
      <template #header>
        <h3 class="text-lg font-bold">Choisir un Background</h3>
      </template>
      <template #default>
        <div class="p-4">
          <div v-if="allAvailableBackgrounds.length === 0" class="text-center py-8">
            <p class="text-xl mb-2">🎨</p>
            <p class="text-sm mb-4">Aucun background disponible</p>
            <Button @click="() => { showBackgroundModal = false; router.visit('/shop'); }" variant="primary">
              Aller au Shop
            </Button>
          </div>
          <div v-else class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div v-for="background in allAvailableBackgrounds" :key="background" @click="selectBackground(background)"
                 class="relative cursor-pointer rounded-lg overflow-hidden transition-all duration-200 border-2 aspect-video"
                 :class="[
                   backgroundForm.background === background
                     ? 'border-secondary shadow-lg shadow-secondary/30 scale-105'
                     : 'border-base-300/30 hover:border-secondary/50 hover:scale-105'
                 ]">
              <img :src="background" alt="Background" class="w-full h-full object-cover" />
              <div v-if="backgroundForm.background === background"
                   class="absolute inset-0 bg-secondary/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
          <div class="flex gap-3 justify-end mt-6">
            <Button @click="showBackgroundModal = false" variant="outline" type="button">
              Annuler
            </Button>
            <Button @click="updateBackground" :disabled="backgroundForm.processing">
              {{ backgroundForm.processing ? 'Mise à jour...' : 'Confirmer' }}
            </Button>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>