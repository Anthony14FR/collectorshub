export interface UserFriend {
    id: number;
    username: string;
    level: number;
    avatar?: string;
    isOnCooldown: boolean;
    nextGiftAvailableAt?: string | null;
    hasGiftToClaim: boolean;
    giftId?: number;
  }
  
export interface UserFriendGift {
    id: number;
    sender_id: number;
    receiver_id: number;
    amount: number;
    is_claimed: boolean;
    sent_at: string;
    claimed_at?: string;
  }
  
export interface FriendRequest {
    id: number;
    user: {
      id: number;
      username: string;
      level: number;
      avatar?: string;
    };
  }
  
export interface User {
    id: number;
    username: string;
    email: string;
    level: number;
    avatar?: string;
  }
  
export interface GiftReward {
    amount: number;
    senderName: string;
    senderAvatar?: string;
    senderId: number;
    isMultiple?: boolean;
  }