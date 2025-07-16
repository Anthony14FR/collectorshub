import { User } from "./user";

export interface UserFriendGift {
    id: number;
    sender_id: number;
    receiver_id: number;
    amount: number;
    is_claimed: boolean;
    sent_at: string;
    claimed_at: string | null;
}

export interface UserFriend {
    id: number;
    username: string;
    level: number;
    hasSentGiftToday: boolean;
    hasGiftToClaim: boolean;
    giftId: number | null;
}

export interface FriendRequest {
    id: number;
    user: User;
}