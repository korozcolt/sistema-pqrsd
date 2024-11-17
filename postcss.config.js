import postcssPurgecss from '@fullhuman/postcss-purgecss';

const purgecss = postcssPurgecss({
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    safelist: [
        'whatsapp-float',
        'go-top',
        /^bx-/,
        /^owl-/,
        /^animate__/,
    ]
});

export default {
    plugins: {
        'tailwindcss/nesting': 'postcss-nesting',
        tailwindcss: {},
        autoprefixer: {},
        ...(process.env.NODE_ENV === 'production' ? { '@fullhuman/postcss-purgecss': purgecss } : {})
    }
};
