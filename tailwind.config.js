/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [ "./back/*.php"],
  theme: {
    colors: {
      primary: 'rgb(var(--color-primary))',
      secondary: 'rgb(var(--color-secondary))',
      vertfonce: 'rgb(var(--color-vertfonce))',
      textprimary: 'rgb(var(--text-color-primary))',
      textsecondary: 'rgb(var(--text-color-secondary))',
    },
    extend: {
      fontFamily: {
        sans: ['"Bacasime Antique"', 'serif'], 
      },
    },
  },
  plugins: [],
}


