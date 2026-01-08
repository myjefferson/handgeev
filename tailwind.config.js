/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#e6fffe',
                    100: '#b3fffc',
                    200: '#80fffa',
                    300: '#4dfff8',
                    400: '#1afff6',
                    500: '#08fff0',
                    600: '#00e6d8',
                    700: '#00b3a8',
                    800: '#008078',
                    900: '#004d48',
                },
                dark: {
                    800: '#1e293b',
                    900: '#0f172a',
                }
            }
        },
    },
    plugins: [],
}
