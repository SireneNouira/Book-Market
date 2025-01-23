/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/*.php",
    "./public/**/*.php"
  ],
  theme: {
    colors: {
      main: "rgb(var(--color-primary))",
      mainopacity: "rgba(var(--color-opacity))",
      secondary: "rgb(var(--color-secondary))",
      vertfonce: "rgb(var(--color-vertfonce))",
      black: "rgb(var(--text-primary))",
      grey:  "rgb(var(--grey))",
    },
    extend: {
      fontFamily: { sans: ['"Bacasime Antique"', "serif"] },
    },
    plugins: [],
  },
};
