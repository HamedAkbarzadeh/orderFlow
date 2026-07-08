// resources/js/Pages/Orders/Orders.constants.ts
export const PAYMENT_TYPES = {
    CASH: 'نقدی',
    INSTALLMENT: 'امانی',
    INSTALLMENT_PLAN: 'قسطی',
    PREPAYMENT: 'بیعانه',
    ON_DELIVERY: 'پرداخت در محل'
} as const;

export const ORDER_STATUSES = {
    PENDING: 'در انتظار',
    DELIVERED: 'تحویل داده شده'
} as const;
