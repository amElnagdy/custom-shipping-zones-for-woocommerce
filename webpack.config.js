const defaults = require("@wordpress/scripts/config/webpack.config");
const BundleAnalyzerPlugin =
  require("webpack-bundle-analyzer").BundleAnalyzerPlugin;

module.exports = {
  ...defaults,
  externals: {
    react: "React",
    "react-dom": "ReactDOM",
  },
};
