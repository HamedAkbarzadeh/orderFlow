import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

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
        // تنظیمات افزونه PWA سازگار با لاراول
        VitePWA({
            outDir: 'public', // قرار دادن فایل‌ها در روت پابلیک
            buildBase: '/',
            injectRegister: false, // جلوگیری از تزریق خودکار (چون در Blade دستی نوشتیم)
            registerType: 'autoUpdate',
            manifest: {
                name: 'مدیریت سفارشات',
                short_name: 'سفارشات',
                description: 'سیستم ثبت و مدیریت سفارشات ویزیتوری',
                theme_color: '#4f46e5',
                background_color: '#f8fafc',
                display: 'standalone',
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
            },
            workbox: {
                navigateFallback: null, // 🔴 بسیار مهم: غیرفعال کردن جستجو برای index.html
                globIgnores: ['**/index.html'], // 🔴 مهم: نادیده گرفتن index.html در کش
                cleanupOutdatedCaches: true,
            },
            devOptions: {
                enabled: true, // فعال کردن PWA در زمان اجرای npm run dev
                type: 'module',
            }
        })
    ],
});
