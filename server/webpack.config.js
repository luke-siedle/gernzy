const path = require('path');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const webpack = require('webpack');

module.exports = {
    entry: './src/resources/js/gernzy.js',
    mode: 'development',
    devtool: 'inline-source-map',
    output: {
        filename: 'gernzy.js',
        path: path.resolve(__dirname, './src/resources/js/dist'),
        libraryTarget: 'umd',
        library: 'Gernzy',
    },
    module: {
        rules: [
            {
                test: /\.js?$/,
                exclude: /(node_modules)/,
                use: 'babel-loader',
            },
        ],
    },
    plugins: [
        new FileManagerPlugin({
            onEnd: {
                copy: [{ source: './src/resources/js/dist/gernzy.js', destination: '../../../public/js' }],
            },
        }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
        }),
    ],
};
