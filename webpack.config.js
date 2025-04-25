const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');

module.exports = {
  entry: './assets/js/admin/app.js',
  output: {
    filename: 'js/app.js',
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/wp-content/plugins/mi-plugin/dist/', // ajusta según tu ruta real
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader'
      },
      {
        test: /\.css$/i,
        use: ['style-loader', 'css-loader']
      }
    ]
  },
  resolve: {
    alias: {
      vue: '@vue/runtime-dom'
    },
    extensions: ['.js', '.vue']
  },
  plugins: [
    new VueLoaderPlugin()
  ],
  mode: 'development' // cámbialo a 'production' para producción
};
