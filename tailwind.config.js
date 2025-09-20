import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.jsx",
    ],

    theme: {
        extend: {
            fontSize: {
                sm: ["13px", { lineHeight: "20px" }],
            },
            colors: {
                primary: {
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                    800: "#1e40af",
                    900: "#1e3a8a",
                },
                secondary: {
                    50: "#fdf2f8",
                    100: "#fce7f3",
                    200: "#fbcfe8",
                    300: "#f9a8d4",
                    400: "#f472b6",
                    500: "#ec4899",
                    600: "#db2777",
                    700: "#be185d",
                    800: "#9d174d",
                    900: "#831843",
                },
                accent: {
                    yellow: "#facc15",
                    green: "#22c55e",
                    red: "#ef4444",
                },
                // Warna netral
                neutral: {
                    50: "#fafafa",
                    100: "#f5f5f5",
                    200: "#e5e5e5",
                    300: "#d4d4d4",
                    400: "#a3a3a3",
                    500: "#737373",
                    600: "#525252",
                    700: "#404040",
                    800: "#262626",
                    900: "#171717",
                },
            },

            fontFamily: {
                sans: ['"Public Sans"', ...defaultTheme.fontFamily.sans],
                // sans: ["Inter", ...defaultTheme.fontFamily.sans],
                heading: ["Poppins", ...defaultTheme.fontFamily.sans],
                raleway: ["Raleway", ...defaultTheme.fontFamily.sans],
                monserrat: ["Monserrat", ...defaultTheme.fontFamily.sans],
                manrope: ["Manrope", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
