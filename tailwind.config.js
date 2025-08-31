/** tailwind.config.js - propuesta para YPFB UI */
module.exports = {
  darkMode: 'class',
  content: [
    './resources/**/*.{blade.php,js,ts,vue}',
    './assets/**/*.{js,ts,css,scss}',
    './resources/views/**/*.blade.php'
  ],
  theme: {
    container: { center: true, padding: '1rem' },
    screens: { sm: '640px', md: '768px', lg: '1024px', xl: '1280px', '2xl': '1536px' },
    extend: {
      colors: {
        ypfb: {
          blue: '#0A3E8F',    // primary
          red: '#E31B23',     // accent / danger
          white: '#FFFFFF'
        },
        neutral: {
          900: '#111827',
          700: '#374151',
          500: '#6B7280',
          300: '#D1D5DB',
          100: '#F3F4F6'
        }
      },
      fontFamily: {
        sans: ["Roboto", "system-ui", "-apple-system", "Segoe UI", "Helvetica", "Arial", "sans-serif"],
        serif: ["Merriweather", "Georgia", "serif"]
      },
      spacing: {
        72: '18rem'
      },
      borderRadius: {
        lg: '0.75rem'
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/line-clamp')
  ],
  safelist: [
    'text-ypfb-blue',
    'bg-ypfb-blue',
    'text-ypfb-red',
    'bg-ypfb-red',
    {
      pattern: /bg-(ypfb|neutral)-(100|300|500|700|900)/
    }
  ]
};
