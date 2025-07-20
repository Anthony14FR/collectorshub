<script setup lang="ts">
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import axios from 'axios';
import { Check, ClipboardList, Gift, PartyPopper } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface DailyQuest {
  id: number;
  key: string;
  name: string;
  description: string;
  target_count: number;
  rewards: {
    xp?: number;
    cash?: number;
    pokeballs?: number;
    masterballs?: number;
  };
  current_count: number;
  is_completed: boolean;
  is_claimed: boolean;
  sort_order: number;
}

interface DailyQuestStats {
  total: number;
  completed: number;
  claimed: number;
  can_claim_bonus: boolean;
  completion_percentage: number;
}

interface Props {
  show: boolean;
  onClose: () => void;
  initialQuests: DailyQuest[];
  initialStats: DailyQuestStats;
}

const props = defineProps<Props>();
const emit = defineEmits(['questClaimed', 'bonusClaimed']);

const quests = ref<DailyQuest[]>([...props.initialQuests]);
const stats = ref<DailyQuestStats>({ ...props.initialStats });
const claiming = ref<number | null>(null);
const claimingBonus = ref(false);

const unclaimedQuests = computed(() => 
  quests.value.filter(q => q.is_completed && !q.is_claimed)
);

const allQuestsCompleted = computed(() => 
  stats.value.total > 0 && stats.value.claimed === stats.value.total
);

const canClaimBonus = computed(() => 
  allQuestsCompleted.value && stats.value.can_claim_bonus
);

const progressPercentage = computed(() => 
  Math.round(stats.value.completion_percentage)
);

const formatRewards = (rewards: DailyQuest['rewards']): string => {
  const parts: string[] = [];
  
  if (rewards.xp) parts.push(`${rewards.xp} XP`);
  if (rewards.cash) parts.push(`${rewards.cash} Cash`);
  if (rewards.pokeballs) parts.push(`${rewards.pokeballs} Pokéballs`);
  if (rewards.masterballs) parts.push(`${rewards.masterballs} Masterballs`);
  
  return parts.join(' + ');
};

const getProgressColor = (current: number, target: number): string => {
  const percentage = (current / target) * 100;
  if (percentage >= 100) return 'bg-success';
  if (percentage >= 75) return 'bg-warning';
  if (percentage >= 50) return 'bg-info';
  return 'bg-primary';
};

const claimQuest = async (questId: number) => {
  if (claiming.value) return;
  
  claiming.value = questId;
  
  try {
    const response = await axios.post('/api/daily-quests/claim', {
      quest_id: questId
    });
    
    if (response.data.success) {
      const questIndex = quests.value.findIndex(q => q.id === questId);
      if (questIndex !== -1) {
        quests.value[questIndex].is_claimed = true;
      }
      
      stats.value.claimed += 1;
      stats.value.can_claim_bonus = stats.value.claimed === stats.value.total;
      
      emit('questClaimed', response.data);
    }
  } catch (error) {
    console.error('Erreur lors de la réclamation:', error);
  } finally {
    claiming.value = null;
  }
};

const claimBonusReward = async () => {
  if (claimingBonus.value) return;
  
  claimingBonus.value = true;
  
  try {
    const response = await axios.post('/api/daily-quests/claim-bonus');
    
    if (response.data.success) {
      stats.value.can_claim_bonus = false;
      emit('bonusClaimed', response.data);
    }
  } catch (error) {
    console.error('Erreur lors de la réclamation du bonus:', error);
  } finally {
    claimingBonus.value = false;
  }
};
</script>

<template>
  <Modal :show="show" @close="onClose" max-width="4xl">
    <template #header>
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center">
          <ClipboardList :size="20" class="text-primary" />
        </div>
        <h3 class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
          Quêtes Quotidiennes
        </h3>
        <Badge v-if="unclaimedQuests.length > 0" variant="error" size="sm">
          {{ unclaimedQuests.length }}
        </Badge>
      </div>
    </template>

    <template #default>
      <div class="space-y-6 p-6">
        <div class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-4 border border-primary/20">
          <div class="flex items-center justify-between mb-3">
            <h4 class="text-lg font-semibold">Progression Quotidienne</h4>
            <div class="text-sm font-medium">
              {{ stats.claimed }} / {{ stats.total }} complétées
            </div>
          </div>
          
          <div class="w-full bg-base-300 rounded-full h-3 mb-3 overflow-hidden">
            <div 
              class="h-full bg-gradient-to-r from-primary to-secondary transition-all duration-500 ease-out"
              :style="{ width: `${progressPercentage}%` }"
            />
          </div>
          
          <div class="text-center">
            <div class="text-2xl font-bold text-primary">{{ progressPercentage }}%</div>
          </div>
        </div>

        <div class="space-y-4">
          <div
            v-for="quest in quests"
            :key="quest.id"
            class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-4 border transition-all duration-300"
            :class="[
              quest.is_completed 
                ? 'border-success/50 bg-gradient-to-br from-success/10 to-success/5' 
                : 'border-base-300/50 hover:border-primary/30'
            ]"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h5 class="font-semibold text-base-content">{{ quest.name }}</h5>
                  <Badge 
                    v-if="quest.is_completed" 
                    variant="success" 
                    size="sm"
                  >
                    <Check :size="12" class="mr-1" />
                    Terminée
                  </Badge>
                  <Badge 
                    v-else-if="quest.current_count > 0" 
                    variant="warning" 
                    size="sm"
                  >
                    En cours
                  </Badge>
                </div>
                
                <p class="text-sm text-base-content/70 mb-3">{{ quest.description }}</p>
                
                <div class="mb-3">
                  <div class="flex justify-between text-sm mb-1">
                    <span class="text-base-content/70">Progression</span>
                    <span class="font-medium">
                      {{ Math.min(quest.current_count, quest.target_count) }} / {{ quest.target_count }}
                    </span>
                  </div>
                  <div class="w-full bg-base-300 rounded-full h-2 overflow-hidden">
                    <div 
                      class="h-full transition-all duration-300"
                      :class="getProgressColor(quest.current_count, quest.target_count)"
                      :style="{ width: `${Math.min((quest.current_count / quest.target_count) * 100, 100)}%` }"
                    />
                  </div>
                </div>
                
                <div class="text-sm">
                  <span class="text-base-content/70">Récompenses: </span>
                  <span class="font-medium text-primary">{{ formatRewards(quest.rewards) }}</span>
                </div>
              </div>
              
              <div class="ml-4">
                <Button
                  v-if="quest.is_completed && !quest.is_claimed"
                  @click="claimQuest(quest.id)"
                  variant="success"
                  size="sm"
                  :disabled="claiming === quest.id"
                  :loading="claiming === quest.id"
                >
                  {{ claiming === quest.id ? 'Réclamation...' : 'Récupérer' }}
                </Button>
                <Button
                  v-else-if="quest.is_claimed"
                  variant="success"
                  size="sm"
                  disabled
                >
                  <Check :size="14" class="mr-1" />
                  Réclamée
                </Button>
                <Button
                  v-else
                  variant="outline"
                  size="sm"
                  disabled
                >
                  En cours
                </Button>
              </div>
            </div>
          </div>
        </div>

        <div 
          v-if="allQuestsCompleted"
          class="bg-gradient-to-br from-warning/20 to-accent/20 rounded-xl p-6 border border-warning/30"
        >
          <div class="text-center">
            <PartyPopper :size="48" class="text-warning mx-auto mb-3" />
            <h4 class="text-xl font-bold text-warning mb-2">Bonus Quotidien !</h4>
            <p class="text-base-content/80 mb-4">
              Félicitations ! Vous avez terminé toutes les quêtes quotidiennes !
            </p>
            <div class="mb-4">
              <span class="text-lg font-semibold bg-gradient-to-r from-warning to-accent bg-clip-text text-transparent">
                2500 XP + 3000 Cash + 10 Pokéballs
              </span>
            </div>
            <Button
              v-if="canClaimBonus"
              @click="claimBonusReward"
              variant="secondary"
              size="lg"
              :disabled="claimingBonus"
              :loading="claimingBonus"
            >
              <Gift v-if="!claimingBonus" :size="18" class="mr-2" />
              {{ claimingBonus ? 'Réclamation...' : 'Récupérer le Bonus' }}
            </Button>
            <Button
              v-else
              variant="success"
              size="lg"
              disabled
            >
              <Check :size="18" class="mr-1" />
              Bonus Réclamé
            </Button>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>