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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'fade-classic': 'linear-gradient(20deg, #e5eeff 0%, #ECF3FF 100%)',
                'fade-ai': 'linear-gradient(20deg, #E6F5F5 0%, #DCF1FF 50%, #DADAF0 100%)',
                'classic-mode': 'linear-gradient(20deg, #D0E0F1 0%, #B8E2FF 100%)',
                'fade-mode': 'linear-gradient(20deg, #91A8F5 0%, #0DFEFF 100%)',
                'ai-summary': 'linear-gradient(#3B82F600 0%, #FFFFFF 100%)',
                'info-gradient': 'linear-gradient(#287ED033 0%, #287ED000 100%)',
                'warning-gradient': 'linear-gradient(#E0922026 0%, #E0922000 100%)',
                'danger-gradient': 'linear-gradient(#D5171926 0%, #D5171900 100%)',
            },
            colors: {
                fade: {
                    surface: '#FFFFFF',
                    accent: '#3B82F6',
                    'secondary-accent': '#0E2958',
                    'accent-hover': '#3A7FEF',
                    
                    text:{
                        primary: '#0E2958',
                        secondary: '#5A6984',
                        accent: '#14379A',
                        highlight: '#14379A',
                        inverse: '#FFFFFF'
                    },
                    
                    muted: '#5A6984',
                    border: '#CBE2FF',
                    'light-border': '#D9E9FC',
    
                    status: {
                        success: '#3B82F6',   
                        warning: '#E09220',
                        danger: '#D51719',
                    },

                    security: {
                        safe: '#3BB273',
                        info: '#287ED0',
                        warning: '#E09220',
                        danger: '#D51719',
                    },

                    'dark-gray': '#414E67',
                    'light-gray': '#8190AA',
                    'dusty-blue': '#1C5285',

                    ai:{
                        'menu': '#CBE2FF',
                    },

                    classic: {
                        'menu': '#414E67',
                        'dark-gray': '#6F7890',
                        'light-gray': '#CDD2DF'
                    },

                    priority: {
                        'high': '#7300FF',
                        'medium': '#B070FF',
                        'low': '#C8AEE9',
                        'read': '#8190AA'
                    }
                },
            },
        },
    },

    plugins: [forms],
};
