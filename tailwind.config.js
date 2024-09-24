/* eslint-disable no-undef */
/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx,vue}'],
  theme: {
    fontFamily: {
      sans: ['poppins', 'system-ui']
    },
    extend: {
      colors: {
        'wc-green': '#3fa649',
        'wc-blue-light': '#214559',
        'wc-blue': '#0a3148',
        'wc-blue-button': '#183d52',
        'wc-grey': '#edf1f4'
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms')({
      //strategy: 'class' // only generate classes
    })
  ]
}
