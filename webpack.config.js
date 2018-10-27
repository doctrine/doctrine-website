const path = require('path'),
    isDevMode = process.env.NODE_ENV !== 'production',
    MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    mode: isDevMode ? 'development' : 'production',
    entry: {
        'index': path.join(__dirname, 'source/styles/index.scss'),
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
            chunkFilename: 'css/[id].css',
        })
    ],
    module: {
        rules: [{
            test: /\.scss$/,
            use: [
                MiniCssExtractPlugin.loader,
                "css-loader",
                "sass-loader"
            ]
        }]
    },
    output: {
        path: path.resolve(__dirname, '.webpack-build'),
        filename: 'js/[name].js',
        crossOriginLoading: 'anonymous',
    }
};