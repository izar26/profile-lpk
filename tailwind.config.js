import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbitePlugin from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Lato', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },

            colors: {
                gold: {
                    50:  '#fdf8ee',
                    100: '#f6e7c7',
                    200: '#e9d59a',
                    300: '#dcbc6f',
                    400: '#caa24a',
                    500: '#b58828',   // Warna utama
                    600: '#9c6f18',
                    700: '#7b5713',
                    800: '#5a4010',
                    900: '#3c2b0c',
                },
            },

            boxShadow: {
                'soft': '0 2px 8px rgba(0,0,0,0.06)',
                'gold': '0 4px 14px rgba(186, 150, 50, 0.25)',
            }
        },
    },

    plugins: [
        forms,
        flowbitePlugin
    ],
};
