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

export interface OrderItem {
    id: number;
    product_id: number;
    product_attribute_id: number | null;
    quantity: number;
    unit_price: number;
}

export interface Order {
    id: number;
    customer_id: number;
    delivery_date: string;
    installment_date: string | null;
    payment_type: string;
    discount: number;
    status: string;
    summary_text: string;
    items: OrderItem[];
}
