var webpack = require('webpack');
var precss = require('precss');
var autoprefixer = require('autoprefixer');

var CleanWebpackPlugin = require('clean-webpack-plugin');
var CommonsChunkPlugin = require("webpack/lib/optimize/CommonsChunkPlugin");
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var LoaderOptionsPlugin = require("webpack/lib/LoaderOptionsPlugin");
var UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');

exports.extractBundle = function (options) {
    var entry = {};
    entry[options.name] = options.entries;

    return {
        entry: entry,
        plugins: [
            // Extract bundle and manifest files. Manifest is needed for reliable caching.
            new CommonsChunkPlugin({names: [options.name, 'commons'], children: true, minChunks: 3})
        ]
    };
};

exports.loadersAndPluginsForVariousTypes = function () {
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
                    loader: process.env.npm_lifecycle_event == 'build' ? 'file-loader?name=[path][name].[hash].[ext]' : 'file-loader?name=[path][name].[ext]'
                }
            ]
        },
        plugins: [
            new ExtractTextPlugin({
                filename: process.env.npm_lifecycle_event == 'build' ? '[name].[contenthash].css' : '[name].css', // Don't use [contenthash] in dev environment, it increases compilation time.
                allChunks: false
            }),
            new LoaderOptionsPlugin({
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
                {
                    test: /\.js$/,
                    loader: 'babel-loader'
                }, {
                    test: /\.css$/,
                    use: [
                        'style-loader',
                        {
                            loader: 'css-loader',
                            options: {importLoaders: 1}
                        },
                        {
                            loader: 'postcss-loader',
                            options: {
                                plugins: function () {
                                    return [
                                        require('precss'),
                                        require('autoprefixer')
                                    ];
                                }
                            }
                        }
                    ]
                }, {
                    test: /\.scss$/,
                    use: [
                        'style-loader',
                        {
                            loader: 'css-loader',
                            options: {importLoaders: 1}
                        },
                        {
                            loader: 'postcss-loader',
                            options: {
                                plugins: function () {
                                    return [
                                        require('precss'),
                                        require('autoprefixer')
                                    ];
                                }
                            }
                        },
                        'sass-loader'
                    ]
                }, {
                    test: /\.(gif|jpg|png)$/,
                    loader: 'file-loader?name=[path][name].[hash].[ext]'
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
        plugins: [
            new UglifyJsPlugin({
                compress: {
                    warnings: false
                }
            })
        ]
    };
};
