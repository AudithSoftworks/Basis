var toolset = require('./storage/build/scripts/webpack/toolset.js');

var DefinePlugin = require("webpack/lib/DefinePlugin");
var ManifestPlugin = require('webpack-manifest-plugin');

var path = require('path');
var merge = require('webpack-merge');
var validate = require('webpack-validator');

var PATHS = {
    js: path.join(__dirname, 'resources', 'assets', 'javascripts'),
    scss: path.join(__dirname, 'resources', 'assets', 'sass'),
    css: path.join(__dirname, 'resources', 'assets', 'stylesheets'),
    build: path.join(__dirname, 'public', 'build')
};

var common = {
    entry: {
        register: path.join(PATHS.js, 'register.js'),
        login: path.join(PATHS.js, 'login.js'),
        auth: path.join(PATHS.js, 'auth.js'),
        file: path.join(PATHS.js, 'file.js'),
        admin: path.join(PATHS.js, 'admin.js')
    },
    output: {
        path: PATHS.build,
        publicPath: '/build/',
        filename: process.env.npm_lifecycle_event == 'build' ? '[name].[chunkhash].js' : '[name].js', // Don't use [chunkhash] in dev environment, it increases compilation time.
        chunkFilename: '[chunkhash].js' // This is used for require.ensure. The setup will work without but this is useful to set.
    },
    plugins: [
        new ManifestPlugin({
            fileName: 'rev-manifest.json'
        })
    ],
    resolve: {
        alias: {
            pace: 'pace-progress/pace.js',
            jquery: 'jquery/src/jquery'
        }
    }
};

var config;

// Detect how npm is run and branch based on that
switch (process.env.npm_lifecycle_event) {
    case 'build': // For production environment.
        config = merge(
            common,
            {
                devtool: 'source-map'
            },
            toolset.clean(PATHS.build),
            toolset.loadersAndPluginsForVariousTypes(),
            // parts.extractBundle({name: 'auth', entries: [path.join(PATHS.js, 'auth.js')]}),
            // parts.extractBundle({name: 'file', entries: [path.join(PATHS.js, 'file.js')]}),
            // parts.extractBundle({name: 'admin', entries: [path.join(PATHS.js, 'admin.js')]}),
            toolset.minify()
        );
        break;
    default: // For a dev environment.
        config = merge(
            common,
            {
                devtool: 'eval-source-map'
            },
            toolset.clean(PATHS.build),
            // parts.styles()
            toolset.loadersAndPluginsForVariousTypes()
        );
        break;
}

module.exports = validate(config);
