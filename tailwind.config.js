import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    // darkMode: 'class',
    // darkMode: 'media',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./node_modules/flowbite/**/*.js"
    ],

    theme: {
        extend: {
            fontFamily: {
                // sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // sans: ['SF Pro Display', 'sans-serif'],
                // sans: ['Open Sans', 'sans-serif'],
            //    sans: ['Inter', 'sans-serif'],
            sans: ['SF Pro Display', 'sans-serif'],
            // sans:['Roboto', 'sans-serif'],
        },
            colors: {
                primary: {"50":"#f5f3ff","100":"#ede9fe","200":"#ddd6fe","300":"#c4b5fd","400":"#a78bfa","500":"#8b5cf6","600":"#7c3aed","700":"#6d28d9","800":"#5b21b6","900":"#4c1d95","950":"#2e1065"}
            },
        },
    },

    // plugins: [forms, typography],
    plugins:  [
        forms, typography,
        require('flowbite/plugin')
    ],

};
