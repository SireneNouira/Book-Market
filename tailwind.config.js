/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/*.php",
    "./public/**/*.php",
    "./back/*.php",
    "./back/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        main: "rgb(var(--color-primary))",
        mainMenu: "rgba(var(--color-primary-opacity))",
        mainopacity: "rgba(var(--color-opacity))",
        secondary: "rgb(var(--color-secondary))",
        vertfonce: "rgb(var(--color-vertfonce))",
        black: "rgb(var(--text-primary))",
        grey: "rgb(var(--grey))",
        white: "rgb(var(--white))",
        // red: "rgb(var(--red))",
      },
      fontFamily: {
        sans: ['Helvetica', 'Arial', 'sans-serif'], // Police moderne et simple
      },
      scrollbar: {
        none: {
          "&::-webkit-scrollbar": {
            display: "none",
          },
          "scrollbar-width": "none",
        },
      },
    },
  },
  plugins: [], // Les plugins doivent être ici, en dehors de `theme`
};