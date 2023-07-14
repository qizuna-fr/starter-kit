const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')
const customColors = require ('./tailwind_variables.json')



/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        'templates/**/*.html.twig',
        'templates/*.html.twig',
        'assets/js/**/*.js',
        'assets/styles/**/*.css',
        'assets/styles/*.css',
        'assets/js/**/*.jsx', // Si vous utilisez des fichiers React JSX
    ],
    theme: {
        fontSize: {
            sm: '0.8rem',
            'xs': '0.75rem',
            'base': '1rem',
            'xl': '1.25rem',
            '2xl': '1.563rem',
            '3xl': '1.953rem',
            '4xl': '2.441rem',
            '5xl': '3.052rem',
            '6xl': '3.815rem',
            '7xl': '4,768rem',
            '8xl': '5.960rem',
            '9xl': '7.450rem',
            '10xl': '9.313rem',
            '11xl': '11.641rem',
            '12xl': '14.551rem',
        },
        colors: {
            'brandPrincipal': customColors.qizunaOrange,
            'brandSecondary': customColors.qizunaBlue,
            'brandThird': customColors.qizunaGray,
            ...colors

        },
        extend: {
            fontFamily: {
                text: ['"Cambay"', ...defaultTheme.fontFamily.sans],
                title: ['"Ubuntu"', ...defaultTheme.fontFamily.sans]
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
