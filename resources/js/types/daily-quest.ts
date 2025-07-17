export interface DailyQuest {
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
  
export interface DailyQuestStats {
    total: number;
    completed: number;
    claimed: number;
    can_claim_bonus: boolean;
    completion_percentage: number;
  }
  
export interface DailyQuestProgress {
    user_id: number;
    daily_quest_id: number;
    date: string;
    current_count: number;
    is_completed: boolean;
    is_claimed: boolean;
    completed_at?: string;
    claimed_at?: string;
  }
  
export interface DailyQuestClaimResponse {
    success: boolean;
    message: string;
    rewards?: {
      xp?: number;
      cash?: number;
      pokeballs?: number;
      masterballs?: number;
    };
  }