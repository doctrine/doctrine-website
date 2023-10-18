const path = require('path'),
    glob = require('glob'),
    isDevMode = process.env.NODE_ENV !== 'production',
    isWatching = process.env.WEBPACK_WATCH === '1',
    { ProvidePlugin } = require('webpack'),
    MiniCssExtractPlugin = require('mini-css-extract-plugin'),
    MergeIntoSingleFilePlugin = require('webpack-merge-and-include-globally'),
    { PurgeCSSPlugin } = require('purgecss-webpack-plugin');

const plugins = () => {
    let plugins = [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
            chunkFilename: 'css/[id].css',
        }),
        new ProvidePlugin({
            $: 'jquery',
            'window.$': 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
        }),
        new MergeIntoSingleFilePlugin({
            files: {
                "js/prismjs.js": [
                    'node_modules/prismjs/components/prism-core.min.js',
                    'node_modules/prismjs/components/prism-markup.min.js',
                    'node_modules/prismjs/components/prism-markup-templating.min.js',
                    'node_modules/prismjs/components/prism-php.min.js',
                    'node_modules/prismjs/components/prism-php-extras.min.js',
                    'node_modules/prismjs/components/prism-phpdoc.min.js',
                    'node_modules/prismjs/components/prism-sql.min.js',
                    'node_modules/prismjs/components/prism-bash.min.js',
                    'node_modules/prismjs/components/prism-clike.min.js',
                    'node_modules/prismjs/components/prism-git.min.js',
                    'node_modules/prismjs/components/prism-json.min.js',
                ],
                "css/prismjs.css": [
                    'node_modules/prismjs/themes/prism-twilight.css',
                ]
            }
        })
    ];

    // Purgecss significantly slows down the compilation time so we'll skip it
    // when we run `npm run watch`
    if (!isWatching) {
        plugins.push(
            new PurgeCSSPlugin({
                safelist: [/^ais/, /^carbon/, /^badge/, /^modal-backdrop/],
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
                type: 'asset/resource'
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
