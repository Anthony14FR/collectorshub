export const getRarityLabel = (rarity: string): string => {
  switch (rarity) {
  case 'normal': return 'Normal';
  case 'rare': return 'Rare';
  case 'epic': return 'Épique';
  case 'legendary': return 'Légendaire';
  default: return rarity;
  }
};

export const getRarityColor = (rarity: string): string => {
  switch (rarity) {
  case 'normal': return 'text-base-content bg-base-200/50 border-base-300/30';
  case 'rare': return 'text-info bg-info/10 border-info/30';
  case 'epic': return 'text-secondary bg-secondary/10 border-secondary/30';
  case 'legendary': return 'text-warning bg-warning/10 border-warning/30';
  default: return 'text-base-content/70 bg-base-200/50 border-base-300/30';
  }
};

export const getRarityDotColor = (rarity: string): string => {
  switch (rarity) {
  case 'normal': return 'bg-base-content/50';
  case 'rare': return 'bg-info';
  case 'epic': return 'bg-purple-500';
  case 'legendary': return 'bg-warning';
  default: return 'bg-base-content/50';
  }
};

export const getFormattedDuration = (minutes: number): string => {
  if (minutes < 1) {
    const seconds = Math.round(minutes * 60);
    return seconds + 's';
  } else if (minutes < 60) {
    const wholeMinutes = Math.floor(minutes);
    const remainingSeconds = Math.round((minutes - wholeMinutes) * 60);
    if (remainingSeconds === 0) {
      return wholeMinutes + 'min';
    }
    return wholeMinutes + 'min ' + remainingSeconds + 's';
  } else {
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = Math.floor(minutes % 60);
    
    let result = hours + 'h';
    if (remainingMinutes > 0) {
      result += ' ' + remainingMinutes + 'min';
    }
    return result;
  }
};

export const getRewardLabel = (reward: any, items?: Array<{id: number, name: string}>): string => {
  switch (reward.type) {
  case 'cash': return `${reward.amount} Cash`;
  case 'xp': return `${reward.amount} XP`;
  case 'pokeball': return `${reward.amount} Pokéball`;
  case 'masterball': return `${reward.amount} Masterball`;
  case 'item': 
    const item = items?.find(i => i.id === reward.item_id);
    return `${reward.quantity} ${item?.name || 'Item'}`;
  default: return reward.type;
  }
};

export const getRequirementLabel = (req: any): string => {
  if (req.type === 'rarity') {
    return `${req.quantity} ${getRarityLabel(req.value)}`;
  } else {
    return `${req.quantity} ${req.value}`;
  }
};

export const getStatusColor = (isActive: boolean): string => {
  return isActive 
    ? 'text-success bg-success/10 border-success/30'
    : 'text-error bg-error/10 border-error/30';
};

export const getStatusLabel = (isActive: boolean): string => {
  return isActive ? 'Active' : 'Inactive';
};

export const capitalizeFirst = (str: string): string => {
  return str.charAt(0).toUpperCase() + str.slice(1);
};

export const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}; 