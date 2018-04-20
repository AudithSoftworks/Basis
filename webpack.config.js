const toolset = require('./storage/build/scripts/webpack/toolset.js');

const DefinePlugin = require("webpack/lib/DefinePlugin");
const ManifestPlugin = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const path = require('path');
const merge = require('webpack-merge');
const dirname = path.resolve();

const PATHS = {
    js: path.join(dirname, 'resources', 'assets', 'javascripts'),
    scss: path.join(dirname, 'resources', 'assets', 'sass'),
    css: path.join(dirname, 'resources', 'assets', 'stylesheets'),
    build: path.join(dirname, 'public', 'build')
};

let common = {
    mode: process.env.NODE_ENV === 'development' ? 'development' : 'production',
    entry: {
        register: path.join(PATHS.js, 'register.js'),
        login: path.join(PATHS.js, 'login.js'),
        auth: path.join(PATHS.js, 'auth.js'),
        file: path.join(PATHS.js, 'file.js'),
    },
    output: {
        path: PATHS.build,
        publicPath: '/build/',
        filename: process.env.NODE_ENV === 'development' ? '[name].js' : '[name].[contenthash].js',
        chunkFilename: '[name].[contenthash].js' // This is used for require.ensure. The setup will work without but this is useful to set.
    },
    plugins: [
        new ManifestPlugin({
            fileName: 'rev-manifest.json',
            publicPath: '/'
        }),

        new MiniCssExtractPlugin({
            filename: "[name].[contenthash].css",
        }),

        // ensure that we get a production build of any dependencies this is primarily for React, where this removes 179KB from the bundle
        new DefinePlugin({
            'process.env.NODE_ENV': '"production"'
        })
    ],
    resolve: {
        alias: {
            pace: 'pace-progress/pace.js',
            jquery: 'jquery/src/jquery'
        }
    }
};

let config;

// Detect how npm is run and branch based on that
switch (process.env.NODE_ENV) {
    case 'production':
        config = merge(
            common,
            toolset.clean(PATHS.build),
            toolset.loadersAndPluginsForVariousTypes(),
            toolset.extractBundles(),
            toolset.minify()
        );
        break;
    default:
        config = merge(
            common,
            toolset.clean(PATHS.build),
            toolset.loadersAndPluginsForVariousTypes(),
            toolset.extractBundles()
        );
        break;
}

module.exports = config;
