const parts = require('./storage/build/scripts/webpack/parts.js');

const DefinePlugin = require("webpack/lib/DefinePlugin");
const ManifestPlugin = require('webpack-manifest-plugin');

const path = require('path');
const merge = require('webpack-merge');
const validate = require('webpack-validator');

const PATHS = {
    js: path.join(__dirname, 'resources', 'assets', 'javascripts'),
    scss: path.join(__dirname, 'resources', 'assets', 'sass'),
    css: path.join(__dirname, 'resources', 'assets', 'stylesheets'),
    build: path.join(__dirname, 'public', 'build')
};

const common = {
    entry: {},
    output: {
        path: PATHS.build,
        publicPath: '/build/',
        filename: '[name].[chunkhash].js',
        chunkFilename: '[chunkhash].js' // This is used for require.ensure. The setup will work without but this is useful to set.
    },
    plugins: [
        new ManifestPlugin({
            fileName: 'rev-manifest.json'
        })
    ]
};

var config;

// Detect how npm is run and branch based on that
switch (process.env.npm_lifecycle_event) {
    case 'build':
    //     config = merge(
    //         common,
    //         {
    //             devtool: 'source-map'
    //         }
    //     );
    //     break;
    default:
        config = merge(
            common,
            {
                devtool: 'eval-source-map'
            },
            parts.clean(PATHS.build),

            parts.extractStyles(),
            // parts.styles(),

            // parts.extractBundle({name: 'file', entries: [path.join(PATHS.js, 'file.js')]}),
            parts.extractBundle({name: 'auth', entries: [path.join(PATHS.js, 'auth.js')]})

            // parts.minify()
        );
}

module.exports = validate(config);
