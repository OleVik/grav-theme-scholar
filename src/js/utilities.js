/**
 * Get value from GET-parameter.
 * @param {string} parameterName GET-query parameter name.
 * @returns {string}
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

/**
 * Check whether object is empty
 * @param {object} o Object to check.
 * @returns {boolean}
 * @see https://stackoverflow.com/a/51207685/603387
 */
function isEmpty(obj) {
  return Object.keys(obj).every(k => !Object.keys(obj[k]).length);
}

export { findGetParameter, isEmpty };
