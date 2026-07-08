// resources/js/Pages/Customers/Customers.types.ts
export interface Customer {
    id: number;
    store_name: string;
    contact_name: string;
    phone: string;
    address: string;
    created_at?: string;
}
