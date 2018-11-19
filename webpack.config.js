const path = require('path'),
    glob = require('glob'),
    isDevMode = process.env.NODE_ENV !== 'production',
    webpack = require('webpack'),
    MiniCssExtractPlugin = require('mini-css-extract-plugin'),
    PurgecssPlugin = require('purgecss-webpack-plugin');

module.exports = {
    mode: isDevMode ? 'development' : 'production',
    entry: {
        'index': path.join(__dirname, 'source/styles/index.scss'),
        'bundle': path.join(__dirname, 'source/js/index.js'),
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
            chunkFilename: 'css/[id].css',
        }),
        new PurgecssPlugin({
            whitelistPatterns: [/^ais/],
            paths: []
                .concat(glob.sync(__dirname + '/templates/**/*.twig'))
                .concat(glob.sync(__dirname + '/source/**/*.html'))
        }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            'window.$': 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
        })
    ],
    module: {
        rules: [
            {
                'test': /\.js$/,
            },
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    "sass-loader"
                ]
            },
            {
                test: /\.(png|svg|jpg|gif|woff|woff2|eot|ttf|otf|.svg)$/,
                loader: 'file-loader',
                options: {
                    name: 'assets/[name].[ext]',
                    publicPath: '/'
                }
            }
        ]
    },
    output: {
        path: path.resolve(__dirname, '.webpack-build'),
        publicPath: '/',
        filename: 'js/[name].js',
        crossOriginLoading: 'anonymous',
    }
};