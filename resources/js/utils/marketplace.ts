import type {
  MarketplaceListing,
  PokedexEntry,
  NormalizedPokemon,
  BasePokemon
} from '@/types/marketplace';


export const formatPrice = (price: number): string => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' ₽';
};

export const getRarityColor = (rarity: string): string => {
  const colors: Record<string, string> = {
    normal: 'text-base-content/70',
    rare: 'text-blue-400',
    epic: 'text-purple-400',
    legendary: 'text-orange-400',
  };
  return colors[rarity] || colors.normal;
};

export const getRarityStars = (rarity: string): number => {
  const stars: Record<string, number> = {
    normal: 1,
    rare: 2,
    epic: 3,
    legendary: 4,
  };
  return stars[rarity] || 1;
};

export const isValidPrice = (price: number): boolean => {
  return price >= 1;
};

export const canSellPokemon = (pokemon: PokedexEntry): boolean => {
  return !pokemon.is_in_team;
};


export const normalizePokemonData = (listing: MarketplaceListing): NormalizedPokemon => {
  const pokemonEntry = listing.pokemon;
  const basePokemon = pokemonEntry.pokemon;

  return {
    entryId: pokemonEntry.id,
    pokemonId: basePokemon.id,
    name: basePokemon.name,
    level: pokemonEntry.level,
    rarity: basePokemon.rarity,
    types: basePokemon.types,
    hp: basePokemon.hp,
    attack: basePokemon.attack,
    defense: basePokemon.defense,
    speed: basePokemon.speed,
    is_shiny: basePokemon.is_shiny,
    is_in_team: pokemonEntry.is_in_team,
    is_favorite: pokemonEntry.is_favorite,
  };
};


export const normalizePokedexEntry = (entry: PokedexEntry): NormalizedPokemon => {
  return {
    entryId: entry.id,
    pokemonId: entry.pokemon.id,
    name: entry.pokemon.name,
    level: entry.level,
    rarity: entry.pokemon.rarity,
    types: entry.pokemon.types,
    hp: entry.pokemon.hp,
    attack: entry.pokemon.attack,
    defense: entry.pokemon.defense,
    speed: entry.pokemon.speed,
    is_shiny: entry.pokemon.is_shiny,
    is_in_team: entry.is_in_team,
    is_favorite: entry.is_favorite,
  };
};


export const parseTypes = (types: string | any[]): string[] => {
  if (!types) return [];

  if (typeof types === 'string') {
    try {
      return JSON.parse(types);
    } catch (e) {
      return [];
    }
  }

  if (Array.isArray(types)) {
    return types.map(type =>
      typeof type === 'object' && type.name ? type.name : type
    );
  }

  return [];
};


export const getPokemonImageUrl = (pokemon: NormalizedPokemon | BasePokemon): string => {
  const id = 'pokemonId' in pokemon ? pokemon.pokemonId : pokemon.id;
  const isShiny = pokemon.is_shiny;

  if (isShiny) {
    return `/images/pokemon-gifs/${id - 1000}_S.gif`;
  }
  return `/images/pokemon-gifs/${id}.gif`;
};


export const filterListings = (
  listings: MarketplaceListing[],
  filters: {
    rarity?: string;
    type?: string;
    isShiny?: boolean;
    minPrice?: number;
    maxPrice?: number;
  }
): MarketplaceListing[] => {
  return listings.filter(listing => {
    const pokemon = normalizePokemonData(listing);

    if (filters.rarity && pokemon.rarity !== filters.rarity) return false;
    if (filters.type && !pokemon.types.includes(filters.type)) return false;
    if (filters.isShiny !== undefined && pokemon.is_shiny !== filters.isShiny) return false;
    if (filters.minPrice && listing.price < filters.minPrice) return false;
    if (filters.maxPrice && listing.price > filters.maxPrice) return false;

    return true;
  });
};


export const calculateMarketplaceStats = (listings: MarketplaceListing[]) => {
  const totalListings = listings.length;
  const shinyListings = listings.filter(l => normalizePokemonData(l).is_shiny).length;
  const totalValue = listings.reduce((sum, l) => sum + l.price, 0);
  const averagePrice = totalListings > 0 ? Math.round(totalValue / totalListings) : 0;

  return {
    totalListings,
    shinyListings,
    averagePrice,
  };
}; 