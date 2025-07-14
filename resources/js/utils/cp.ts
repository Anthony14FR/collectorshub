import type { Pokedex } from '@/types/pokedex';


export function calculateTeamCP(teamPokemons: Pokedex[]): number {
  return teamPokemons.reduce((totalCP, pokemon) => {
    return totalCP + pokemon.cp;
  }, 0);
}

export function getCPRarityClass(cp: number): string {
  if (cp >= 10000) {
    return 'text-orange-400 border-orange-500/40';
  } else if (cp >= 5000) {
    return 'text-purple-400 border-purple-500/40';
  } else if (cp >= 2000) {
    return 'text-blue-400 border-blue-500/40';
  } else {
    return 'text-base-content/70 border-base-content/30';
  }
}