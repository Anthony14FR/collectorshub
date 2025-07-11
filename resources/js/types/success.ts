export interface Success {
  id: number;
  title: string;
  description: string;
  image: string;
  cash_reward: number;
  xp_reward: number;
}

export interface UserSuccess {
  id: number;
  success_id: number;
  user_id: number;
  unlocked_at: string;
  claimed_at: string | null;
  is_claimed: boolean;
  success: Success;
} 