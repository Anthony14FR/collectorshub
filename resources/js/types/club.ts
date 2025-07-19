export interface Club {
  id: number;
  name: string;
  description: string;
  icon: string;
  leader_id: number;
  total_cp: number;
  created_at: string;
  updated_at: string;
}

export interface ClubRequest {
  id: number;
  club_id: number;
  user_id: number;
  status: 'pending' | 'accepted' | 'rejected';
  created_at: string;
  updated_at: string;
}

export interface ClubMember {
  id: number;
  username: string;
  level: number;
  avatar: string;
  pivot: {
    role: 'leader' | 'member';
  };
} 