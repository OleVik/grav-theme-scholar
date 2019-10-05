const {
    invertedColors,
    colorPreferences
  } = require("magica11y/lib/invertedColors"),
  {
    prefersContrast,
    contrastPreferences
  } = require("magica11y/lib/prefersContrast"),
  color = require("css-color-converter"),
  chromatism = require("chromatism");

module.exports = {
  convert: function(colour) {
    return chromatism.convert(colour).hex;
  },
  complementary: function(colour) {
    return chromatism.complementary(colour).hex;
  },
  triad: function(colour) {
    return chromatism.triad(colour).hex;
  },
  tetrad: function(colour) {
    return chromatism.tetrad(colour).hex;
  },
  uniformComplementary: function(colour) {
    return chromatism.uniformComplementary(colour).hex;
  },
  uniformTriad: function(colour) {
    return chromatism.uniformTriad(colour).hex;
  },
  uniformTetrad: function(colour) {
    return chromatism.uniformTetrad(colour).hex;
  },
  mid: function(colourOne, colourTwo) {
    return chromatism.mid(colourOne, colourTwo).hex;
  },
  invert: function(colour) {
    return chromatism.invert(colour).hex;
  },
  invertLightness: function(colour) {
    return chromatism.invertLightness(colour).hex;
  },
  multiply: function(colourOne, colourTwo) {
    return chromatism.multiply(colourOne, colourTwo).hex;
  },
  adjacent: function(degrees, sections, colour) {
    return chromatism.adjacent(degrees, sections, colour).hex;
  },
  fade: function(amount, colourFrom, colourTo) {
    return chromatism.fade(amount, colourFrom, colourTo).hex;
  },
  shade: function(percent, colour) {
    return chromatism.shade(percent, colour).hex;
  },
  saturation: function(percent, colour) {
    return chromatism.saturation(percent, colour).hex;
  },
  brightness: function(percent, colour) {
    return chromatism.brightness(percent, colour).hex;
  },
  hue: function(degrees, colour) {
    return chromatism.hue(degrees, colour).hex;
  },
  contrast: function(contrastCoeff, colour) {
    return chromatism.contrast(contrastCoeff, colour).hex;
  },
  greyscale: function(colour) {
    return chromatism.greyscale(colour).hex;
  },
  sepia: function(colour) {
    return chromatism.sepia(colour).hex;
  },
  contrastRatio: function(colour) {
    return chromatism.contrastRatio(colour).hex;
  },
  adapt: function(colour, illuminantColour, sourceIlluminant) {
    return chromatism.adapt(colour, illuminantColour, sourceIlluminant).hex;
  },
  difference: function(colourOne, colourTwo, luminanceWeight, chromaWeight) {
    return chromatism.difference(
      colourOne,
      colourTwo,
      luminanceWeight,
      chromaWeight
    ).hex;
  },
  temperature: function(colour) {
    return chromatism.temperature(colour).hex;
  } /* ,
  darken: function(value, frac) {
    var darken = 1 - parseFloat(frac);
    var rgba = color(value).toRgbaArray();
    var r = rgba[0] * darken;
    var g = rgba[1] * darken;
    var b = rgba[2] * darken;
    return color([r, g, b]).toHexString();
  } */
};
