const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');
const typography = require('@tailwindcss/typography');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/views/**/*.blade.php',
        './resources/css/**/*.css',
        './resources/**/*.vue',
        './resources/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Custom colors using CSS variables
                success: {
                    DEFAULT: 'var(--success-color)',
                    light: 'var(--success-light)',
                    dark: 'var(--success-dark)',
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: 'var(--success-color)',
                    600: 'var(--success-dark)',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
                primary: {
                    DEFAULT: 'var(--primary-color)',
                    light: 'var(--primary-light)',
                    dark: 'var(--primary-dark)',
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: 'var(--primary-color)',
                    600: 'var(--primary-dark)',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                warning: {
                    DEFAULT: 'var(--warning-color)',
                    light: 'var(--warning-light)',
                    dark: 'var(--warning-dark)',
                    50: '#fefce8',
                    100: '#fef9c3',
                    200: '#fef08a',
                    300: '#fde047',
                    400: '#facc15',
                    500: 'var(--warning-color)',
                    600: 'var(--warning-dark)',
                    700: '#a16207',
                    800: '#854d0e',
                    900: '#713f12',
                },
                danger: {
                    DEFAULT: 'var(--danger-color)',
                    light: 'var(--danger-light)',
                    dark: 'var(--danger-dark)',
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: 'var(--danger-color)',
                    600: 'var(--danger-dark)',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
            },
            animation: {
                'shimmer': 'shimmer 2s infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'bounce-slow': 'bounce 2s infinite',
            },
            keyframes: {
                shimmer: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                },
            },
        },
    },

    plugins: [forms, typography],
};
