/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        slate: {
          850: '#1E293B',
          900: '#0F172A',
        },
        tech: {
          blue: '#0284C7',
          'blue-hover': '#0369A1',
          'blue-light': '#E0F2FE',
        },
        surface: {
          50: '#F8FAFC',
          100: '#F1F5F9',
          200: '#E2E8F0',
          300: '#CBD5E1',
        },
        gold: {
          400: '#C9A35E',
          500: '#B8944F',
        }
      },
      fontFamily: {
        sans: ['Inter', 'Roboto', 'system-ui', '-apple-system', 'Segoe UI', 'Arial', 'sans-serif'],
      },
      boxShadow: {
        'premium': '0 8px 32px rgba(15, 23, 42, 0.06)',
        'premium-hover': '0 32px 64px rgba(15, 23, 42, 0.14)',
        'glass': '0 4px 24px rgba(0, 0, 0, 0.08)',
      },
      borderRadius: {
        '2xl': '1rem',
        '3xl': '1.5rem',
        '4xl': '2rem',
      }
    },
  },
  plugins: [],
}
