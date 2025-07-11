<script setup>
import { ref } from "vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import Alert from "@/Components/UI/Alert.vue";
import BackgroundEffects from "@/Components/UI/BackgroundEffects.vue";
import Button from "@/Components/UI/Button.vue";
import ShopItemsList from "@/Components/Shop/ShopItemsList.vue";
import BuyItemModal from "@/Components/Shop/BuyItemModal.vue";
import InventoryModal from "@/Components/Shop/InventoryModal.vue";

const props = defineProps({
  user: Object,
  items: Array,
  inventory: Array,
  errors: Object,
});

const selectedItem = ref(null);
const showBuyModal = ref(false);
const showInventoryModal = ref(false);
const activeTab = ref("items");
const processing = ref(false);

const form = useForm({
  item_id: null,
  quantity: 1,
});

const openBuyModal = (item) => {
  selectedItem.value = item;
  showBuyModal.value = true;
};

const buyItem = (data) => {
  processing.value = true;
  form.item_id = data.item_id;
  form.quantity = data.quantity;

  form.post(route("shop.buy"), {
    onSuccess: () => {
      showBuyModal.value = false;
      selectedItem.value = null;
    },
    onFinish: () => {
      processing.value = false;
    }
  });
};

const formatPrice = (price) => {
  return price.toLocaleString();
};

const handleTabChange = (tab) => {
  activeTab.value = tab;
};
</script>

<template>
  <Head title="Boutique" />

  <div class="h-screen w-screen overflow-x-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-x-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            BOUTIQUE
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            ITEMS & OBJETS SPÉCIAUX
          </p>
        </div>
      </div>
      <div class="absolute left-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
            <h3 class="text-sm font-bold tracking-wider">NAVIGATION</h3>
          </div>
          <div class="p-3">
            <Link href="/me">
              <Button variant="secondary" size="sm" class="w-full">
                Retour à l'accueil
              </Button>
            </Link>
          </div>
        </div>
      </div>

      <!-- Right Sidebar -->
      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="bg-gradient-to-r from-success/10 to-success/5 px-3 py-2 border-b border-success/20">
            <h4 class="text-xs font-bold tracking-wider">PORTE-MONNAIE</h4>
          </div>
          <div class="p-3 text-center">
            <div class="text-2xl font-bold text-success">
              {{ formatPrice(user.cash) }}
            </div>
            <div class="text-xs text-base-content/70">Votre solde</div>
          </div>
        </div>
      </div>

      <div class="absolute top-32 left-1/2 -translate-x-1/2 w-[800px] max-h-[700px]">
        <div v-if="$page.props.flash?.success" class="mb-4">
          <Alert type="success" :message="$page.props.flash.success" />
        </div>
        <div v-if="$page.props.errors?.message" class="mb-4">
          <Alert type="error" :message="$page.props.errors.message" />
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <ShopItemsList 
            :items="items"
            :inventory="inventory"
            :userAvatars="user.unlocked_avatars || []"
            :activeTab="activeTab"
            @buy="openBuyModal"
            @changeTab="handleTabChange"
          />
        </div>
      </div>

      <BuyItemModal 
        :show="showBuyModal"
        :item="selectedItem"
        :userCash="user.cash"
        :processing="processing"
        @close="showBuyModal = false"
        @confirm="buyItem"
      />

      <InventoryModal 
        :show="showInventoryModal"
        :inventory="inventory"
        @close="showInventoryModal = false"
      />
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
