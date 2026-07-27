import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    // Verde Clínico — color principal del isotipo y acentos
                    DEFAULT:  '#3B8E87',
                    50:  '#eef7f6',
                    100: '#d4edec',
                    200: '#a9dbd9',
                    300: '#7dc9c6',
                    400: '#52b7b3',
                    500: '#3B8E87',   // principal
                    600: '#2e716b',
                    700: '#225450',
                    800: '#163835',
                    900: '#0b1c1a',
                },
                ink: {
                    // Gris Antracita — texto primario
                    dark:    '#333333',
                    // Gris Medio — texto secundario y cuerpo
                    medium:  '#666666',
                },
            },
        },
    },

    plugins: [forms],
};
