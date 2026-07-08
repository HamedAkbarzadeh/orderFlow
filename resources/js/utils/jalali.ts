import moment from "moment-jalaali";

// فعال‌سازی تقویم فارسی (اعداد لاتین نگه داشته می‌شود چون قراره در دیتابیس/فرم استفاده بشه)
moment.loadPersian({ usePersianDigits: false, dialect: "persian-modern" });

export const JALALI_MONTHS = [
    "فروردین",
    "اردیبهشت",
    "خرداد",
    "تیر",
    "مرداد",
    "شهریور",
    "مهر",
    "آبان",
    "آذر",
    "دی",
    "بهمن",
    "اسفند",
];

/**
 * تبدیل تاریخ میلادی (YYYY-MM-DD) به رشته نمایشی شمسی به فرم 1403/04/17
 */
export function toJalaliDisplay(gregorianDate?: string | null): string {
    if (!gregorianDate) return "-";
    const m = moment(gregorianDate, "YYYY-MM-DD");
    if (!m.isValid()) return "-";
    return m.format("jYYYY/jMM/jDD");
}

/**
 * تبدیل تاریخ میلادی به رشته با نام ماه فارسی، مثلاً «۱۷ تیر ۱۴۰۳»
 */
export function toJalaliLongDisplay(gregorianDate?: string | null): string {
    if (!gregorianDate) return "-";
    const m = moment(gregorianDate, "YYYY-MM-DD");
    if (!m.isValid()) return "-";
    return `${m.jDate()} ${JALALI_MONTHS[m.jMonth()]} ${m.jYear()}`;
}

/**
 * تعداد روزهای یک ماه شمسی خاص (jm از ۱ تا ۱۲)
 */
export function daysInJalaliMonth(jy: number, jm: number): number {
    return moment.jDaysInMonth(jy, jm - 1);
}

/**
 * تبدیل سال/ماه/روز شمسی به تاریخ میلادی با فرمت YYYY-MM-DD (برای ارسال به بک‌اند)
 */
export function jalaliToGregorian(jy: number, jm: number, jd: number): string {
    return moment(`${jy}/${jm}/${jd}`, "jYYYY/jM/jD").format("YYYY-MM-DD");
}

/**
 * سال شمسی جاری (برای مقداردهی پیش‌فرض دیت‌پیکر)
 */
export function currentJalaliYear(): number {
    return moment().jYear();
}
