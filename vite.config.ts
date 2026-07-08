import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa'; // این خط اضافه شود

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // تنظیمات افزونه PWA
        VitePWA({
            outDir: 'public', // خروجی فایل‌ها در پوشه پابلیک لاراول قرار می‌گیرد
            buildBase: '/',
            registerType: 'autoUpdate',
            manifest: {
                name: 'مدیریت سفارشات',
                short_name: 'سفارشات',
                description: 'سیستم ثبت و مدیریت سفارشات ویزیتوری',
                theme_color: '#4f46e5', // رنگ تم هدر اپلیکیشن (رنگ ایندیگو)
                background_color: '#f8fafc',
                display: 'standalone', // حذف نوار مرورگر و اجرای شبیه اپلیکیشن
                dir: 'rtl',
                lang: 'fa-IR',
                icons: [
                    {
                        src: '/icon-192x192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ]
            }
        })
    ],
});
