const rgbHex = require("rgb-hex");

module.exports = {
  map: { inline: true },
  plugins: [
    require("postcss-import"),
    require("postcss-media-variables"),
    require("postcss-color-mod-function")({
      unresolved: "warn",
      stringifier(color) {
        return "#" + rgbHex(color.toRGBLegacy());
      },
      transformVars: false,
    }),
    require("postcss-media-variables"),
    require("postcss-nested"),
    require("stylelint")({
      configFile: "./stylelint.config.js",
    }),
    require("postcss-preset-env")({ stage: 0, preserve: false }),
    require("./local_modules/node_modules/postcss-wcag-contrast"),
    require("postcss-reporter")({
      clearReportedMessages: true,
      noPlugin: true,
    }),
    require("cssnano")({
      preset: [
        "default",
        {
          discardComments: {
            removeAll: true,
          },
        },
      ],
    }),
  ],
};
