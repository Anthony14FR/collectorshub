export type Inventory = {
    id: number;
    user_id: number;
    item_id: number;
    quantity: number;
    item: Item;
}

export type Item = {
    id: number;
    name: string;
    description: string;
    type: string;
    effect: any;
    price: number;
    rarity: string;
}