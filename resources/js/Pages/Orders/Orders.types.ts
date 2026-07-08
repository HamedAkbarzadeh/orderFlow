// resources/js/Pages/Orders/Orders.types.ts
export interface Customer {
    id: number;
    store_name: string;
    contact_name: string;
    phone: string;
    address: string;
}

export interface ProductAttribute {
    id: number;
    key: string;
    value: string;
    unit: string | null;
    price_increase: number;
}

export interface Product {
    id: number;
    name: string;
    price: number;
    attributes: ProductAttribute[];
}
