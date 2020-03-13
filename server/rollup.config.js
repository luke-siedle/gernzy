// rollup.config.js
import babel from 'rollup-plugin-babel';

export default {
    input: './src/resources/js/gernzy.js',
    output: {
        file: './src/resources/js/dist/gernzy.js',
        format: 'es',
    },
    plugins: [
        babel({
            exclude: 'node_modules/**', // only transpile our source code
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
