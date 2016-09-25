const webpack = require('webpack');
const precss = require('precss');
const autoprefixer = require('autoprefixer');

const CleanWebpackPlugin = require('clean-webpack-plugin');
const CommonsChunkPlugin = require("webpack/lib/optimize/CommonsChunkPlugin");
const ExtractTextPlugin = require('extract-text-webpack-plugin');

exports.extractBundle = function (options) {
    const entry = {};
    entry[options.name] = options.entries;

    return {
        entry: entry,
        plugins: [
            // Extract bundle and manifest files. Manifest is needed for reliable caching.
            new CommonsChunkPlugin({names: [options.name, 'manifest']})
        ]
    };
};

exports.extractStyles = function () {
    return {
        module: {
            loaders: [
                {
                    test: /\.css$/,
                    loader: ExtractTextPlugin.extract({
                        fallbackLoader: 'style-loader',
                        loader: 'css-loader!postcss-loader'
                    })
                }, {
                    test: /\.scss$/,
                    loader: ExtractTextPlugin.extract({
                        fallbackLoader: 'style-loader',
                        loader: 'css-loader!postcss-loader!sass-loader'
                    })
                }, {
                    test: /\.(gif|jpg|png)$/,
                    loader: 'file-loader?name=[path][name].[hash].[ext]'
                }
            ]
        },
        plugins: [
            new ExtractTextPlugin({
                filename: '[name].[contenthash].css',
                allChunks: false
            }),
            new webpack.LoaderOptionsPlugin({
                // test: /\.xxx$/, // may apply this only for some modules
                options: {
                    postcss: function () {
                        return [precss, autoprefixer];
                    }
                }
            })

        ]
    };
};

exports.styles = function () {
    return {
        module: {
            loaders: [
                {test: /\.css$/, loader: 'style-loader!css-loader!postcss-loader'},
                {test: /\.scss$/, loader: 'style-loader!css-loader!postcss-loader!sass-loader'},
                {test: /\.(gif|jpg|png)$/, loader: 'file-loader?name=[path][name].[hash].[ext]'}
            ]
        },
        postcss: function () {
            return [precss, autoprefixer];
        }
    };
};

exports.setupStyles = function (options) {
    const entry = {};
    entry[options.name] = options.entries;

    return {
        entry: entry,
        module: {
            loaders: [
                {test: /\.scss$/, loader: 'style!css!autoprefixer!sass'},
                {test: /\.css$/, loader: 'style!css'}
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
        plugins: [
            new webpack.optimize.UglifyJsPlugin({
                compress: {
                    warnings: false
                }
            })
        ]
    };
};
