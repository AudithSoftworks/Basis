const CleanWebpackPlugin = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const UglifyJsWebpackPlugin = require('uglifyjs-webpack-plugin');

exports.extractBundles = function () {
    return {
        optimization: {
            splitChunks: {
                cacheGroups: {
                    node_modules: {
                        name: 'vendor',
                        test: /node_modules/,
                        chunks: 'all'
                    },
                    vendor_scss: {
                        name: 'vendor',
                        test: /resources\/assets\/sass/,
                        chunks: 'all'
                    }
                }
            }
        }
    };
};

exports.loadersAndPluginsForVariousTypes = function () {
    return {
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'postcss-loader'
                    ]
                },
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'postcss-loader',
                        'sass-loader'
                    ]
                },
                {
                    test: /\.(gif|jpg|png)$/,
                    use: process.env.NODE_ENV === 'production' ? 'file-loader?name=[path][name].[hash].[ext]' : 'file-loader?name=[path][name].[ext]'
                },
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: "babel-loader"
                }
            ]
        }
    };
};

exports.clean = function (path) {
    return {
        plugins: [
            new CleanWebpackPlugin([path], {
                root: process.cwd() // Without `root` CleanWebpackPlugin won't point to our project and will fail to work.
            })
        ]
    }
};

exports.minify = function () {
    return {
        optimization: {
            minimizer: [
                new UglifyJsWebpackPlugin({
                    cache: true,
                    parallel: true,
                    sourceMap: true
                }),
                new OptimizeCSSAssetsPlugin({})
            ]
        }
    };
};
