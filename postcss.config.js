module.exports = {
  map: { inline: false },
  plugins: [
    require("postcss-import"),
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
