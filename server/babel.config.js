module.exports = {
    presets: [
        [
            '@babel/preset-env',
            {
                targets: ['last 2 versions', 'ie >= 11'],
            },
        ],
    ],
};
