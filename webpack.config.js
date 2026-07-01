let fs = require('fs');
let path = require('path');
let webpack = require('webpack');
let TerserPlugin = require('terser-webpack-plugin');
let { VueLoaderPlugin } = require('vue-loader');

let cliMode = getCliMode(process.argv);
let isProd = process.env.NODE_ENV === 'production' || cliMode === 'production';
let projectRoot = __dirname;

let webpackConfig = {
    mode: isProd ? 'production' : 'development',

    entry: path.resolve(projectRoot, 'application/assets/js/app/app.js'),

    output: {
        path: path.resolve(projectRoot, 'application/assets/js/build'),
        publicPath: '/application/assets/js/build/',
        filename: 'app.build.js',
        chunkFilename: '[name].js',
        clean: true
    },

    module: {
        rules: [
            {
                test: /\.vue$/,
                include: [
                    path.resolve(projectRoot, 'application/assets/vue')
                ],
                loader: 'vue-loader'
            },
            {
                test: /\.js$/,
                include: [
                    path.resolve(projectRoot, 'application/assets/js'),
                    path.resolve(projectRoot, 'application/assets/vue')
                ]
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            },
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                type: 'asset/resource',
                generator: {
                    filename: 'images/[name][hash][ext][query]'
                }
            }
        ]
    },

    resolve: {
        extensions: ['.js', '.vue', '.json'],

        modules: [
            path.resolve(projectRoot, 'node_modules'),
            'node_modules'
        ],

        alias: {
            'vue$': path.resolve(projectRoot, 'node_modules/vue/dist/vue.esm-bundler.js')
        }
    },

    devtool: isProd ? 'hidden-source-map' : 'inline-source-map',

    optimization: {
        minimize: isProd,
        minimizer: [
            new TerserPlugin({
                extractComments: false,
                terserOptions: {
                    compress: {
                        passes: 2,
                        drop_console: true
                    },
                    format: {
                        comments: false
                    }
                }
            })
        ]
    },

    plugins: [
        new VueLoaderPlugin(),
        new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: JSON.stringify(true),
            __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false)
        })
    ]
};

loadPackageConfigs(webpackConfig, {
    fs,
    path,
    projectRoot
});

module.exports = webpackConfig;

function getCliMode(argv)
{
    // Support both "--mode production" and "--mode=production"

    let modeArgIndex = argv.indexOf('--mode');

    if (modeArgIndex !== -1 && argv[modeArgIndex + 1]) {
        return argv[modeArgIndex + 1];
    }

    let modeArg = argv.find((argument) => argument.startsWith('--mode='));

    if (!modeArg) {
        return null;
    }

    return modeArg.split('=')[1];
}

function loadPackageConfigs(webpackConfig, context)
{
    let packageConfigDirectory = path.resolve(projectRoot, 'application/webpack');

    if (!context.fs.existsSync(packageConfigDirectory)) {
        return;
    }

    // Load each package config and apply it to the main webpack config

    let packageConfigFiles = context.fs.readdirSync(packageConfigDirectory)
        .filter((fileName) => fileName.endsWith('.js'))
        .sort();

    for (let packageConfigFileName of packageConfigFiles) {
        let packageConfigPath = path.resolve(packageConfigDirectory, packageConfigFileName);
        let applyPackageConfig = require(packageConfigPath);

        if (typeof applyPackageConfig !== 'function') {
            continue;
        }

        applyPackageConfig(webpackConfig, context);
    }
}
