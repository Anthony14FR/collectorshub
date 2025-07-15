export interface LeaderboardUser {
    rank: number;
    id: number;
    username: string;
    level: number;
    avatar?: string;
    value?: number;
    team_cp?: number;
    team_pokemons?: any[];
}

export interface LeaderboardData {
    top: LeaderboardUser[];
    current_user: LeaderboardUser;
}