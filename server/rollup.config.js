// rollup.config.js
import babel from 'rollup-plugin-babel';

export default {
    external: ['jquery'],
    input: './src/resources/js/gernzy.js',
    output: {
        file: './src/resources/js/dist/index.js',
        format: 'es',
        globals: {
            jquery: '$',
        },
    },
    plugins: [
        babel({
            runtimeHelpers: true,
            exclude: 'node_modules/**', // only transpile our source code
            plugins: ['@babel/plugin-transform-runtime'],
            presets: [
                [
                    '@babel/preset-env',
                    {
                        modules: false,
                        targets: ['last 2 versions', 'ie >= 11'],
                    },
                ],
            ],
        }),
    ],
};
