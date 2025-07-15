export const REWARD_TYPES = [
  { value: 'cash', label: 'Cash', icon: '$' },
  { value: 'xp', label: 'XP', icon: 'XP' },
  { value: 'pokeball', label: 'Pokéball', icon: '⚾' },
  { value: 'masterball', label: 'Masterball', icon: '🏀' },
  { value: 'item', label: 'Item', icon: '🎁' }
];

export const REQUIREMENT_TYPES = [
  { value: 'rarity', label: 'Rareté' },
  { value: 'type', label: 'Type' }
];

export const RARITIES = ['normal', 'rare', 'epic', 'legendary'];

export type ExpeditionReward = {
  type: string;
  amount?: number;
  item_id?: number;
  quantity?: number;
};

export type ExpeditionRequirement = {
  type: string;
  value: string;
  quantity: number;
};

export type ExpeditionRarity = 'normal' | 'rare' | 'epic' | 'legendary'; 