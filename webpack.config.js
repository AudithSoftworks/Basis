const toolset = require('./storage/build/scripts/webpack/toolset.js');

const DefinePlugin = require("webpack/lib/DefinePlugin");
const ManifestPlugin = require('webpack-manifest-plugin');

const path = require('path');
const merge = require('webpack-merge');

const PATHS = {
    js: path.join(__dirname, 'resources', 'assets', 'javascripts'),
    scss: path.join(__dirname, 'resources', 'assets', 'sass'),
    css: path.join(__dirname, 'resources', 'assets', 'stylesheets'),
    build: path.join(__dirname, 'public', 'build')
};

let common = {
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

let config;

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
            toolset.extractBundles(),
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
            // toolset.styles()
            toolset.loadersAndPluginsForVariousTypes(),
            toolset.extractBundles()
        );
        break;
}

module.exports = config;
