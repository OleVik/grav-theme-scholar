const rgbHex = require("rgb-hex");

module.exports = {
  map: { inline: true },
  plugins: [
    require("postcss-import"),
    // require("postcss-parcel-import"),
    // require("postcss-contrast")({
    //   light: "#ffffff",
    //   dark: "#000000",
    // }),
    require("postcss-media-variables"),
    require("postcss-custom-properties")({ preserve: false }),
    // require("postcss-functions")({
    //   functions: require("./js/docs/functions.css")
    // }),
    // require("postcss-css-variables")({
    //   variables: require("./js/docs/themes.css").metal.default
    // }),
    require("postcss-color-mod-function")({
      unresolved: "warn",
      stringifier(color) {
        return "#" + rgbHex(color.toRGBLegacy());
      },
      transformVars: false,
    }),
    require("postcss-media-variables"),
    require("postcss-nested"),
    require("postcss-preset-env")({ stage: 0 }),
    require("postcss-wcag-contrast"),
    require("colorguard")({ allowEquivalentNotation: true }),
    require("postcss-reporter")({ clearReportedMessages: true }),
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
