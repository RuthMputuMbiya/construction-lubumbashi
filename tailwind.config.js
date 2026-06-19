// tailwind.config.js

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Http/Livewire/**/*.php",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    
    theme: {
        extend: {
            // Couleurs personnalisées pour le projet de construction
            colors: {
                'primary': {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                'secondary': {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    950: '#022c22',
                },
                'danger': {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                    950: '#450a0a',
                },
                'warning': {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                    950: '#451a03',
                },
                'info': {
                    50: '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#06b6d4',
                    600: '#0891b2',
                    700: '#0e7490',
                    800: '#155e75',
                    900: '#164e63',
                    950: '#083344',
                },
                'dark': {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                }
            },
            
            // Polices personnalisées
            fontFamily: {
                'poppins': ['Poppins', 'sans-serif'],
                'inter': ['Inter', 'sans-serif'],
                'montserrat': ['Montserrat', 'sans-serif'],
            },
            
            // Ombres personnalisées
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'neumorph': '20px 20px 60px #d9d9d9, -20px -20px 60px #ffffff',
                'card': '0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02)',
                'glow': '0 0 20px rgba(59, 130, 246, 0.3)',
            },
            
            // Animations personnalisées
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-in': 'slideIn 0.3s ease-out',
                'slide-up': 'slideUp 0.4s ease-out',
                'bounce-slow': 'bounce 2s infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'spin-slow': 'spin 3s linear infinite',
                'gradient': 'gradient 3s ease infinite',
                'float': 'float 3s ease-in-out infinite',
            },
            
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideIn: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(0)' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(100%)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                gradient: {
                    '0%, 100%': {
                        'background-size': '200% 200%',
                        'background-position': 'left center'
                    },
                    '50%': {
                        'background-size': '200% 200%',
                        'background-position': 'right center'
                    }
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                }
            },
            
            // Arrière-plans
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                'hero-pattern': "url('/images/hero-pattern.svg')",
                'construction': "linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('/images/construction-bg.jpg')",
            },
            
            // Espacements
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
                '144': '36rem',
            },
            
            // Border radius
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            
            // Backdrop blur
            backdropBlur: {
                xs: '2px',
            },
        },
    },
    
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('daisyui'),
        require('tailwind-scrollbar'),
    ],
    
    // Configuration daisyUI
    daisyui: {
        themes: [
            {
                'construction': {
                    'primary': '#1a56db',
                    'primary-focus': '#0e3a8a',
                    'primary-content': '#ffffff',
                    'secondary': '#10b981',
                    'secondary-focus': '#059669',
                    'secondary-content': '#ffffff',
                    'accent': '#f59e0b',
                    'accent-focus': '#d97706',
                    'accent-content': '#ffffff',
                    'neutral': '#1f2937',
                    'neutral-focus': '#111827',
                    'neutral-content': '#ffffff',
                    'base-100': '#ffffff',
                    'base-200': '#f9fafb',
                    'base-300': '#f3f4f6',
                    'base-content': '#111827',
                    'info': '#06b6d4',
                    'success': '#10b981',
                    'warning': '#f59e0b',
                    'error': '#ef4444',
                },
            },
            'light',
            'dark',
        ],
        darkTheme: 'dark',
        base: true,
        styled: true,
        utils: true,
        rtl: false,
        prefix: '',
        logs: true,
    },
    
    // Configuration scrollbar
    variants: {
        scrollbar: ['rounded'],
    },
}