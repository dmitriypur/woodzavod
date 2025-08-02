import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                'primary': '#d9a441',
                'primary-dark': '#b18721',
                'secondary': '#252a5a',
                'accent': '#959493',
                'dark': '#323232',
                'light-gray': '#a3acb1',
                'bg-light': '#f6f6f5',
                'gold-light': '#bea059',
                'gold-lighter': '#d8ca92',
                'green-dark': '#616141',
                'white-custom': '#fbfaff'
            },
            fontFamily: {
                'montserrat': ['Montserrat', 'sans-serif'],
                'evolventa': ['Evolventa', 'sans-serif']
            }
        },
    },
    plugins: [],
};
