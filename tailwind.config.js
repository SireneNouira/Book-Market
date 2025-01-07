/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./back/**/*.php",
    "./front/**/*.html",
    "./front/**/*.php",
    "./front/**/*.js",
    "./front/styles/**/*.css",
  ],
  theme: {
    colors: {
      main: "rgb(var(--color-primary))",
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
