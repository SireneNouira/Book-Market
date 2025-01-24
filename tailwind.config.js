/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/*.php",
    "./public/**/*.php",
    "./back/*.php",
    "./back/**/*.php",
  ],
  theme: {
    colors: {
      main: "rgb(var(--color-primary))",
      mainMenu: "rgba(var(--color-primary-opacity))",
      mainopacity: "rgba(var(--color-opacity))",
      secondary: "rgb(var(--color-secondary))",
      vertfonce: "rgb(var(--color-vertfonce))",
      black: "rgb(var(--text-primary))",
      grey:  "rgb(var(--grey))",
      white: "rgb(var(--white))",
    },
    extend: {
      fontFamily: { sans: ['"Bacasime Antique"', "serif"] },
    },
    plugins: [],
  },
};
