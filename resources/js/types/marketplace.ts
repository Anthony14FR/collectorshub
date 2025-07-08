export interface BasePokemon {
  id: number;
  name: string;
  rarity: 'normal' | 'rare' | 'epic' | 'legendary';
  types: string[];
  hp: number;
  attack: number;
  defense: number;
  speed: number;
  is_shiny: boolean;
}

export interface PokedexEntry {
  id: number;
  user_id: number;
  pokemon_id: number;
  level: number;
  nickname?: string;
  star?: number;
  is_favorite: boolean;
  is_in_team: boolean;
  obtained_at: string;
  pokemon: BasePokemon;
}

export interface MarketplaceListing {
  id: number;
  pokemon_id: number;
  seller_id: number;
  price: number;
  status: 'active' | 'sold' | 'cancelled';
  created_at: string;
  pokemon: PokedexEntry;
  seller: {
    id: number;
    username: string;
  };
}

export interface PriceRange {
  min: number;
  max: number;
}

export interface MarketplaceFilters {
  rarity: string;
  type: string;
  isShiny: string;
  minPrice: string;
  maxPrice: string;
}

export interface MarketplaceStats {
  totalListings: number;
  shinyListings: number;
  averagePrice: number;
  userListings: number;
}

export interface NormalizedPokemon {
  entryId: number;
  pokemonId: number;
  name: string;
  level: number;
  rarity: string;
  types: string[];
  hp: number;
  attack: number;
  defense: number;
  speed: number;
  is_shiny: boolean;
  is_in_team: boolean;
  is_favorite: boolean;
}