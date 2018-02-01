const webpack = require('webpack'); //访问内置的插件
const path = require('path'); //node path
const CleanWebpackPlugin = require('clean-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
//const HtmlWebpackPlugin = require('html-webpack-plugin');


/**
 * 生产环境
 * @since  2018-02-01 17:29:38
 * @author Tan<tandamailzone@gmail.com>
 */
const config = {
    //入口文件
    entry: {
        index: __dirname + '/script/index.js',
        detail: __dirname + '/script/detail.js',
        //部分通用的第三方库
        vendor: [
            __dirname + '/script/module/rem.js',
            __dirname + '/script/vendor/jquery.cookie.js',
        ]
    },
    //输入文件
    output: {
        //打包后输出文件的文件名
        filename: 'script/[name].js',
        //打包后的文件存放的地方
        path: path.resolve(__dirname, 'assets/src/')
    },
    //通过AMD方式载入全局配置
    externals: {
        jquery: 'window.$'
    },

    //开启sourceMap, 生成一个 DataUrl 形式的 SourceMap 文件.
    devtool: 'inline-source-map',

    //加载器
    module: {
        loaders: [
            {
                //CSS加载器(css-loader)
                test: /\.css$/,
                loader: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: [
                        {
                            loader: 'css-loader',
                            options: {
                                minimize: true //css压缩
                            }
                        }
                    ],
                    publicPath: '../'
                })
            },
            {
                //JS加载器(babel-loader)
                test: /\.js$/,
                include: [
                    // 只去解析运行目录下的 src 和 demo 文件夹
                    path.join(process.cwd(), './js')
                ],
                loader: 'babel-loader',
                query: {
                    presets: ['es2015'],
                }
            },
            {
                //图片资源加载器(url-loader)
                test: /.(png|jpg|gif|svg)$/,
                loader: [
                    'url-loader?limit=10240&name=image/[name].[ext]',
                    'image-webpack-loader' //图片压缩
                ]
            },
            {
                //字体资源加载器(url-loader)
                test: /\.(woff)|(svg)|(eot)|(ttf)$/,
                loader: 'url-loader?limit=1024&name=font/[name].[ext]'
            }
        ]
    },

    plugins: [
        //压缩js
        new webpack.optimize.UglifyJsPlugin(),
        //合并压缩css
        new ExtractTextPlugin({
            filename: 'style/[name].css'
        }),
        // 构建优化插件-提取公共代码
        new webpack.optimize.CommonsChunkPlugin({
            //common指引入(import)的文件, vender指entry中的vender
            names: ['common', 'vendor'],
            //重复资源超过阀值及提出来到公共文件中去
            minChunks: 2
        }),
        //在building之前删除以前build过的文件或目录
        new CleanWebpackPlugin(['assets/src/script/*.js', 'assets/src/style/*.css'], {
            root: __dirname, //当前根目录
            verbose: true, //开启在控制台输出信息
            dry: false //启用删除文件
        })
    ]
}


module.exports = config;