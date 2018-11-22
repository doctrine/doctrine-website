const path = require('path'),
    glob = require('glob'),
    isDevMode = process.env.NODE_ENV !== 'production',
    isWatching = process.env.WEBPACK_WATCH === '1',
    webpack = require('webpack'),
    MiniCssExtractPlugin = require('mini-css-extract-plugin'),
    PurgecssPlugin = require('purgecss-webpack-plugin');

const plugins = () => {
    let plugins = [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
            chunkFilename: 'css/[id].css',
        }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            'window.$': 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
        })
    ];

    // Purgecss significantly slows down the compilation time so we'll skip it
    // when we run `npm run watch`
    if (!isWatching) {
        plugins.push(
            new PurgecssPlugin({
                whitelistPatterns: [/^ais/],
                paths: []
                    .concat(glob.sync(__dirname + '/templates/**/*.twig'))
                    .concat(glob.sync(__dirname + '/source/**/*.html'))
                    .concat(glob.sync(__dirname + '/source/**/*.js'))
            })
        )
    }

    return plugins;
};

module.exports = {
    mode: isDevMode ? 'development' : 'production',
    entry: {
        'index': path.join(__dirname, 'source/styles/index.scss'),
        'bundle': path.join(__dirname, 'source/js/index.js'),
    },
    plugins: plugins(),
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
                    publicPath: '/frontend/'
                }
            }
        ]
    },
    output: {
        path: path.resolve(__dirname, '.webpack-build'),
        publicPath: '/frontend/',
        filename: 'js/[name].js',
        crossOriginLoading: 'anonymous',
    }
};