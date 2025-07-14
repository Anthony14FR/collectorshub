import type { PokedexEntry } from './user';

export interface UpgradeRequirement {
  star: number;
  pokemon_id: number | null;
  quantity: number;
  description: string;
}

export interface UpgradeRequirements {
  main_requirement: UpgradeRequirement;
  secondary_requirement?: UpgradeRequirement;
}

export interface UpgradeData {
  requirements: UpgradeRequirements;
  availablePokemons: {
    main_requirement: PokedexEntry[];
    secondary_requirement?: PokedexEntry[];
  };
  canUpgrade: boolean;
}