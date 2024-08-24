const webpack = require("webpack");
const path = require("path");
// const CleanWebpackPlugin = require("clean-webpack-plugin");
const buildFolder = path.resolve(__dirname, "./dist");
const port = 3000;

// Building for development

  console.log("Building for development");

  module.exports = {
    entry: './src/jsx/plataforma.jsx',
    output: {
      filename: "[name].min.js",
    path: path.resolve(__dirname, 'dist'),
    },
    resolve: {
      extensions: ['.js', '.jsx', '.css', '.scss'],
      alias: {
        '@components': path.resolve(__dirname, 'src/components'),
        '@styles': path.resolve(__dirname, 'src/styles'),
        '@': path.resolve(__dirname, '/src'),
      },
      fallback: {
        stream: require.resolve("stream-browserify"),
        path: require.resolve("path-browserify")
      }
    },
    mode: "development",
    devtool: 'inline-source-map',
    externals: {
      // Use external version of React & ReactDOM via WordPress
      react: "React",
      "react-dom": "ReactDOM"
    },
    output: {
      path: buildFolder,
      filename: "[name].js",
      publicPath: "http://localhost:" + port + "/assets/"
    },
    module: {
      rules: [
        {
          test: /\.jsx?$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env', '@babel/preset-react'],
            },
          },
        },
      ],
    },

    plugins: [
      new webpack.HotModuleReplacementPlugin()
      //new CleanWebpackPlugin( [ buildFolder ] )
    ],

    
  }
