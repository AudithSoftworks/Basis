const webpack = require('webpack');
const precss = require('precss');
const autoprefixer = require('autoprefixer');

const CleanWebpackPlugin = require('clean-webpack-plugin');
const CommonsChunkPlugin = require("webpack/lib/optimize/CommonsChunkPlugin");
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const LoaderOptionsPlugin = require("webpack/lib/LoaderOptionsPlugin");
const UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');

exports.extractBundles = function () {
    return {
        plugins: [
            new CommonsChunkPlugin(
                {
                    name: 'vendor',
                    minChunks: function (module /*, countOfHowManyTimesThisModuleIsUsedAcrossAllChunks */) {
                        const context = module.context;

                        // You can perform other similar checks here too. Now we check just node_modules.
                        return context && context.indexOf('node_modules') >= 0;
                    }
                }
            ),
            new CommonsChunkPlugin(
                {
                    name: 'global',
                    minChunks: function (module, countOfHowManyTimesThisModuleIsUsedAcrossAllChunks) {
                        // You can perform other similar checks here too. Now we check just node_modules.
                        // return context && context.indexOf('node_modules') >= 0;
                        return module.resource
                            && module.resource.indexOf('resources/assets/sass/global') >= 0
                            && countOfHowManyTimesThisModuleIsUsedAcrossAllChunks === 2;
                    }
                }
            ),
        ],
    };
};

exports.loadersAndPluginsForVariousTypes = function () {
    return {
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: ExtractTextPlugin.extract({
                        fallback: 'style-loader',
                        use: [{
                            loader: 'css-loader',
                        }, {
                            loader: 'postcss-loader'
                        }]
                    })
                }, {
                    test: /\.scss$/,
                    use: ExtractTextPlugin.extract({
                        fallback: 'style-loader',
                        use: [{
                            loader: 'css-loader',
                        }, {
                            loader: 'postcss-loader'
                        }, {
                            loader: 'sass-loader'
                        }]
                    })
                }, {
                    test: /\.(gif|jpg|png)$/,
                    use: process.env.npm_lifecycle_event == 'build' ? 'file-loader?name=[path][name].[hash].[ext]' : 'file-loader?name=[path][name].[ext]'
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

exports.styles = function ({include, exclude} = {}) {
    return {
        module: {
            rules: [
                {
                    test: /\.css$/,
                    include,
                    exclude,
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
                    use: 'file-loader?name=[path][name].[hash].[ext]'
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
