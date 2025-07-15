<template>
  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <h1 class="text-2xl font-bold mb-6">📢 Envoyer une annonce</h1>

          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Titre de l'annonce
              </label>
              <input id="title" v-model="form.title" type="text"
                     class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                     placeholder="Titre de votre annonce..." required />
            </div>

            <div>
              <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Message
              </label>
              <textarea id="message" v-model="form.message" rows="4"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        placeholder="Votre message..." required></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Destinataires
              </label>
              <div class="mt-2 space-y-2">
                <label class="flex items-center">
                  <input v-model="form.target_type" type="radio" value="all"
                         class="text-indigo-600" />
                  <span class="ml-2">📢 Tous les utilisateurs</span>
                </label>
                <label class="flex items-center">
                  <input v-model="form.target_type" type="radio" value="specific"
                         class="text-indigo-600" />
                  <span class="ml-2">👥 Utilisateurs spécifiques</span>
                </label>
              </div>
            </div>

            <div v-if="form.target_type === 'specific'" class="space-y-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Sélectionner les utilisateurs ({{ form.user_ids.length }} sélectionné(s))
              </label>

              <div
                class="max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md p-3 space-y-2">
                <label v-for="user in users" :key="user.id"
                       class="flex items-center space-x-2 hover:bg-gray-50 dark:hover:bg-gray-700 p-2 rounded cursor-pointer">
                  <input type="checkbox" :value="user.id" v-model="form.user_ids"
                         class="text-indigo-600" />
                  <div class="flex-1">
                    <div class="font-medium">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                  </div>
                </label>
              </div>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
              <button type="button" @click="resetForm"
                      class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Réinitialiser
              </button>
              <button type="submit"
                      class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                📤 Envoyer l'annonce
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    users: Array
  },
  data() {
    return {
      form: {
        title: '',
        message: '',
        target_type: 'all',
        user_ids: []
      }
    }
  },
  methods: {
    resetForm() {
      this.form = {
        title: '',
        message: '',
        target_type: 'all',
        user_ids: []
      }
    },
    submit() {
      this.$inertia.post('/admin/notifications', this.form, {
        onSuccess: () => {
          this.resetForm()
        }
      })
    }
  }
}
</script>