const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");

const path = require('path');
const isDev = process.env.NODE_ENV === 'development';

module.exports = {
    entry: {
        css: './resources/js/css.js',
        //app: './resources/js/app.js'
    },
    output: {
        filename: '[name].min.js',
        path: path.resolve(__dirname, 'public/assets')
    },
  
    module: {
        rules: [
            /*
            {
                test: /\.m?js$/,
                exclude: /node_modules/,
                use: {
                loader: 'babel-loader',
                options: {
                    presets: [
                    ['@babel/preset-env', { targets: "defaults" }]
                    ]
                }
                }
            }
            */
            {
                test: /\.css$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: 'css/[name].css'
                        }
                    }, {
                        loader: 'extract-loader',
                    }, {
                        loader: 'css-loader',
                        options: {
                            url: false
                        }
                    }, {
                        loader: 'string-replace-loader',
                        options: {
                            multiple: [
                                { search: '../img/', replace: '/public/assets/images/', flags: 'g' },
                                { search: '../fonts/', replace: '/public/assets/fonts/', flags: 'g' }
                             ]
                        }
                    }
                ],
            },
        ]
    },
    
    optimization: {
        minimizer: [
            `...`,
            new CssMinimizerPlugin(),
        ],
    },
    
    //devtool: 'source-map',
    mode: isDev ? 'development' : 'production',
    plugins: [],
};