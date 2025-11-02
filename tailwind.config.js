

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        maroon: {
          DEFAULT: "#6e0b0b",
          700: "#7A0E0E",
          500: "#8c1b1b",
        },
        scarlet: "#9d1b1b",
        gold: "#caa15a",
        ok: "#22c55e",
        bad: "#ef4444",
      },
    },
  },
  plugins: [],
};
