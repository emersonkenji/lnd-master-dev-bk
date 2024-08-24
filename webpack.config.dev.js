
const path = require('path');
const { merge } = require('webpack-merge');
// const TerserPlugin = require('terser-webpack-plugin');
// const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
// const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer');
const webpack = require('webpack');

const SpeedMeasurePlugin = require("speed-measure-webpack-plugin");
const smp = new SpeedMeasurePlugin();

module.exports = smp.wrap(merge({
// module.exports = merge({
  // entry: {
  //   dashboard: './src/js/dashboard.jsx',
  //   // print: './src/print.js',
  // },
  mode: 'development',
  output: {
    filename: "[name].min.js",
    // path: path.resolve(__dirname, 'dist'),
  },
  devtool: 'eval-cheap-module-source-map',
  // devtool: 'eval-cheap-source-map',
  optimization: {
    minimize: false,
  },
  cache: {

    type: 'filesystem',
    buildDependencies: {
      config: [__filename],
    },
  },
  resolve: {
    extensions: ['.js', '.jsx', '.css', '.scss'],
    alias: {
      '@components': path.resolve(__dirname, 'src/components'),
      '@': path.resolve(__dirname, 'src'),
    },
    // fallback: {
    //   stream: require.resolve("stream-browserify"),
    //   path: require.resolve("path-browserify")
    // },
    modules: [path.resolve(__dirname, 'src'), 'node_modules'],
    symlinks: false,
  },
  module: {
    rules: [
      // {
      //   test: /\.jsx?$/,
      //   loader: 'esbuild-loader',
      //   options: {
      //     loader: 'jsx',
      //     target: 'es2015',
      //   },
      // },
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env', '@babel/preset-react'],
              cacheDirectory: true,
            },
          },
        ],
      },
      {
        test: /\.(s[ac]|c)ss$/i,
        use: ['style-loader', 'css-loader', 'sass-loader'],
      },
    ],
  },
  plugins: [
  //   new webpack.IgnorePlugin({
  //   resourceRegExp: /^\.\/locale$/,
  //   contextRegExp: /moment$/,
  // }),
    new webpack.HotModuleReplacementPlugin(),
    new BundleAnalyzerPlugin({
      analyzerMode: 'static', // Pode ser 'server', 'static', ou 'disabled'
      openAnalyzer: false,    // Se deve abrir automaticamente o relatório
      reportFilename: 'report.html', // Nome do arquivo do relatório
    }),
  ],
}));
