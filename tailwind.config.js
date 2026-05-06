/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        premium: {
          light: '#ffffff',
          bg: '#f8fafc',
          text: '#0f172a',
          muted: '#64748b',
          emerald: '#059669',
          emeraldHover: '#047857',
          gold: '#d97706',
        }
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      }
    },
  },
  plugins: [],
}
