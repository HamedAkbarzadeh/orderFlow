// resources/js/Pages/Products/Products.types.ts
export interface ProductAttribute {
    id?: number;
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
