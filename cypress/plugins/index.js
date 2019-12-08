// ***********************************************************
// This example plugins/index.js can be used to load plugins
//
// You can change the location of this file or turn off loading
// the plugins file with the 'pluginsFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/plugins-guide
// ***********************************************************

// This function is called when a project is opened or re-opened (e.g. due to
// the project's config changing)

const path = require("path");
const fs = require("fs");

module.exports = (on, config) => {
  // config.env.stylesFolder = path.join(__dirname, "../../css/styles");
  let styles = [];
  var files = fs.readdirSync(path.join(__dirname, "../../css/styles"));
  files.forEach(function(file) {
    styles.push(file);
  });
  config.env.styles = styles;
  return config;
};
