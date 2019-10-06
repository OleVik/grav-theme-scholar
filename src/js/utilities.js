import has from "has-values";

/**
 * Get value from GET-parameter.
 * @param {string} parameterName GET-query parameter name.
 */
function findGetParameter(parameterName, type = null) {
  var result = null,
    tmp = [];
  location.search
    .substr(1)
    .split("&")
    .forEach(function(item) {
      tmp = item.split("=");
      if (tmp[0] === parameterName) {
        result = decodeURIComponent(tmp[1]);
      }
    });
  if (result !== null) {
    return result;
  } else {
    return type;
  }
}

export { findGetParameter, has };
