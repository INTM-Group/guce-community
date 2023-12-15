process.env.TZ = "Europe/Paris";

// Libs
const ScriptExtHtmlWebpackPlugin = require("script-ext-html-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const { WebpackManifestPlugin } = require("webpack-manifest-plugin");
const https = require("https");
const fs = require("fs");
const path = require("path");
const dayjs = require("dayjs");
const localizedFormat = require("dayjs/plugin/localizedFormat");

// Constants
const packageDefinition = require("./package.json");
const countries = ["fr"];
const maxSize = 1024 * 1024 * 1024 * 1024 * 1024;

// Config DayJS
require(`dayjs/locale/${countries[0]}`);
dayjs.locale(countries[0]);
dayjs.extend(localizedFormat);

// Functions
function htmlRedefined(args) {
  let appName = process.env.APP_NAME.toLowerCase();
  args[0].title = `${process.env.APP_NAME} v${
    packageDefinition.version
  } - ${process.env.NODE_ENV.trim().toUpperCase()}`;
  args[0].app = `${appName}`;
  args[0].appStart = `<${appName}>`;
  args[0].appEnd = `</${appName}>`;
  args[0].version = packageDefinition.version;
  args[0].build = dayjs().unix();
  args[0].api_host = process.env.APP_URL;
  args[0].api_version = packageDefinition.version.split(".")[0];
  args[0].clientName = process.env.APP_CLIENT;
  args[0].template = "resources/template.html";
  return args;
}

function downloadHolydays(country, year) {
  console.group(year);
  try {
    const holidays = `storage/public/holydays/${country}/${year}.json`;
    if (!fs.existsSync(holidays)) {
      const request = https.get(`https://date.nager.at/api/v2/PublicHolidays/${year}/${country}`, function(response) {
        if (response.statusCode === 200) {
          let file = fs.createWriteStream(holidays);
          response.pipe(file);
          console.info("Exitoso");
        }
        request.setTimeout(12000, function() {
          console.error("Fallido");
          request.abort();
        });
      });
    } else console.info("Existant");
  } catch (err) {
    console.error(err, country, year);
  }
  console.groupEnd();
}

// Download Holydays
console.group("Téléchargement de vacances");
for (let country of countries) {
  console.group("Téléchargement", country);
  downloadHolydays(country, dayjs().year());
  downloadHolydays(country, dayjs().year() + 1);
  console.groupEnd();
}
console.groupEnd();

// Webpack Configs

const webPackAlias = {
  resolve: {
    alias: {
      "@storage": path.resolve(__dirname, "storage/public"),
      "@public": path.resolve(__dirname, "public")
    }
  }
};

// Development Version
const developmnentConfig = {
  configureWebpack: { ...webPackAlias },
  chainWebpack: config => {
    config.plugins.store.delete("copy");
    config.plugin("html").tap(htmlRedefined);
    config.module
      .rule("images")
      .test(/\.(png|jpe?g|gif|webp|ico)(\?.*)?$/)
      .use("url-loader")
      .loader("url-loader")
      .options({
        limit: maxSize,
        encoding: "base64"
      })
      .end();
  }
};

// online Version
const onlineConfig = {
  ...developmnentConfig,
  publicPath: "/",
  outputDir: "public",
  indexPath: "index.html",
  productionSourceMap: false,
  configureWebpack: {
    ...webPackAlias,
    plugins: [
      new CleanWebpackPlugin({
        cleanOnceBeforeBuildPatterns: ["**/*", "!*.php", "!*.ico"]
      }),
      new WebpackManifestPlugin({
        writeToFileEmit: true,
        seed: {
          build: dayjs().unix()
        }
      })
    ]
  }
};

// Download Version
const downloadConfig = {
  publicPath: "",
  outputDir: `storage/public/${process.env.APP_NAME.toLowerCase()}`,
  indexPath: `${process.env.APP_NAME.toLowerCase()}-v${packageDefinition.version}.html`,
  productionSourceMap: false,
  lintOnSave: true,
  filenameHashing: false,
  css: {
    extract: false
  },
  configureWebpack: {
    ...webPackAlias,
    optimization: {
      splitChunks: {
        cacheGroups: {
          default: false,
          bundle: {
            name: "bundle",
            chunks: "all",
            minChunks: 1,
            reuseExistingChunk: false
          }
        }
      }
    },
    plugins: [
      new ScriptExtHtmlWebpackPlugin({
        inline: /\.js$/
      })
    ]
  },
  chainWebpack: config => {
    config.plugins.store.delete("prefetch");
    config.plugins.store.delete("preload");
    config.plugins.store.delete("copy");
    config.plugin("html").tap(htmlRedefined);
    config.module
      .rule("fonts")
      .test(/\.(woff2?|eot|ttf|otf)(\?.*)?$/)
      .use("url-loader")
      .loader("url-loader")
      .options({
        limit: maxSize,
        encoding: "base64"
      })
      .end();
    config.module
      .rule("images")
      .test(/\.(png|jpe?g|gif|webp|ico)(\?.*)?$/)
      .use("url-loader")
      .loader("url-loader")
      .options({
        limit: maxSize,
        encoding: "base64"
      })
      .end();
  }
};

// Config options
const configsOptions = {
  production: onlineConfig,
  downloaded: downloadConfig,
  development: developmnentConfig
};
console.info("Config: ", process.env.NODE_ENV.trim(), !!configsOptions[process.env.NODE_ENV.trim()]);
module.exports = {
  transpileDependencies: ["vuetify"],
  ...(configsOptions[process.env.NODE_ENV.trim()] || developmnentConfig)
};
