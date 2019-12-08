import * as util from "./utilities.js";

/**
 * Initialize metadata and content search
 * @param {object[]} data An array of objects, each representing a Page
 * @param {array} fields List of fields in each data-object
 * @param {array} options List of options for FlexSearch
 */
function searchFieldInit(data, fields, options) {
  const start = performance.now();
  var dataIndex;
  toggleResults("main aside.search", "main section");
  const FlexSearchOptions = options;
  FlexSearchOptions.doc = {
    id: "url",
    field: fields,
  };
  try {
    dataIndex = new FlexSearch(FlexSearchOptions);
    dataIndex.add(data);
    console.debug(
      `FlexSearch add(n = ${data.length}): ${msToTime(
        performance.now() - start
      )}`
    );
  } catch (error) {
    throw new Error(error);
  }
  try {
    document.querySelector("#query").addEventListener(
      "keyup",
      debounce(function(event) {
        const start = performance.now();
        dataIndex.search(event.srcElement.value, 10).then(function(results) {
          console.debug(
            `FlexSearch search(n = ${results.length}): ${msToTime(
              performance.now() - start
            )}`
          );
          renderResults(".search-results", results);
          console.debug(
            `FlexSearch render(n = ${results.length}): ${msToTime(
              performance.now() - start
            )}`
          );
        });
      }, 200)
    );
    document.querySelector("#query").addEventListener("focus", function(event) {
      event.target.setAttribute("aria-expanded", "true");
      document
        .querySelector("main aside.search")
        .setAttribute("aria-expanded", "true");
    });
    document.querySelector("#query").addEventListener("blur", function(event) {
      event.target.setAttribute("aria-expanded", "false");
    });
  } catch (error) {
    throw new Error(error);
  }
}

/**
 * Initialize metadata and content search
 * @param {object[]} data An array of objects, each representing a Page
 * @param {array} fields List of fields in each data-object
 */
function searchPageInit(data, fields, options) {
  const start = performance.now();
  var dataIndex, categoriesSelector, tagsSelector;
  const FlexSearchOptions = options;
  FlexSearchOptions.doc = {
    id: "url",
    field: fields,
  };
  const query = {
    title: util.findGetParameter("title", ""),
    date: util.findGetParameter("date", ""),
    categories: util.findGetParameter("category", ""),
    tags: util.findGetParameter("tag", ""),
    content: decodeURIComponent(util.findGetParameter("content", "")),
  };
  try {
    dataIndex = new FlexSearch(FlexSearchOptions);
    dataIndex.add(data);
    console.debug(
      `FlexSearch add(n = ${data.length}): ${msToTime(
        performance.now() - start
      )}`
    );
    if (query.title !== "") {
      document.querySelector(".search-query #title").value = query.title;
    }
    if (query.date !== "") {
      document.querySelector(".search-query #date").value = query.date;
    }
    if (query.categories !== "") {
      query.categories = query.categories.split(",");
      categoriesSelector.setValue(query.categories);
    } else {
      query.categories = [];
    }
    if (query.tags !== "") {
      query.tags = query.tags.split(",");
      tagsSelector.setValue(query.tags);
    } else {
      query.tags = [];
    }
    if (query.content !== "") {
      document.querySelector(".search-query #content").value = query.content;
    }
    if (util.has(query)) {
      search(dataIndex, fields, query);
    }
  } catch (error) {
    throw new Error(error);
  }
  try {
    document.querySelector(".search-query #title").addEventListener(
      "keyup",
      debounce(function(event) {
        query.title = event.srcElement.value;
        search(dataIndex, fields, query);
      }, 200)
    );
    document
      .querySelector(".search-query #date")
      .addEventListener("change", function(event) {
        query.date = event.srcElement.value;
        search(dataIndex, fields, query);
      });
    if (typeof categoriesSelector !== "undefined") {
      categoriesSelector.on("selectr.select", function(option) {
        if (option.selected) {
          query.categories.push(option.value);
          search(dataIndex, fields, query);
        }
      });
      categoriesSelector.on("selectr.deselect", function(option) {
        if (!option.selected) {
          if (dataIndex !== -1) {
            query.categories.splice(query.categories.indexOf(option.value), 1);
          }
          search(dataIndex, fields, query);
        }
      });
    }
    if (typeof tagsSelector !== "undefined") {
      tagsSelector.on("selectr.select", function(option) {
        if (option.selected) {
          query.tags.push(option.value);
          search(dataIndex, fields, query);
        }
      });
      tagsSelector.on("selectr.deselect", function(option) {
        if (!option.selected) {
          if (dataIndex !== -1) {
            query.tags.splice(query.tags.indexOf(option.value), 1);
          }
          search(dataIndex, fields, query);
        }
      });
    }
    document.querySelector(".search-query #content").addEventListener(
      "keyup",
      debounce(function(event) {
        query.content = event.srcElement.value;
        search(dataIndex, fields, query);
      }, 200)
    );
  } catch (error) {
    throw new Error(error);
  }
}

/**
 *
 * @param {object} index Data to search
 * @param {array} fields List of fields in each data-object
 * @param {object} query Field constraints
 */
function search(index, fields, query) {
  const start = performance.now();
  const data = index.where(function(doc) {
    return limitSearch(query, doc);
  });
  console.debug(
    `FlexSearch where(n = ${data.length}): ${msToTime(
      performance.now() - start
    )}`
  );
  if (query.title !== "" || query.content !== "") {
    var contentIndex = new FlexSearch({
      profile: "balance",
      encode: "advanced",
      tokenize: "full",
      cache: true,
      async: true,
      doc: {
        id: "url",
        field: fields,
      },
    });
    for (let i = 0; i < data.length; i++) {
      contentIndex.add(data[i]);
    }
    var fieldQuery;
    if (query.title !== "" && query.content === "") {
      fieldQuery = [{ field: "title", query: query.title }];
    } else if (query.title === "" && query.content !== "") {
      fieldQuery = [{ field: "content", query: query.content }];
    } else {
      fieldQuery = [
        {
          field: "title",
          query: query.title,
          bool: "and",
        },
        {
          field: "content",
          query: query.content,
          bool: "or",
        },
      ];
    }
    contentIndex.search(fieldQuery, 10).then(function(results) {
      console.debug(
        `FlexSearch search(n = ${results.length}): ${msToTime(
          performance.now() - start
        )}`
      );
      renderResults(".search-results", results);
      console.debug(
        `FlexSearch render(n = ${results.length}): ${msToTime(
          performance.now() - start
        )}`
      );
    });
  } else if (query.title === "" && query.content === "") {
    renderResults(".search-results", data.slice(0, 9));
    console.debug(
      `FlexSearch render(n = ${data.length}): ${msToTime(
        performance.now() - start
      )}`
    );
  }
}

/**
 * Constrain search results by field values
 * @param {object} query Field constraints
 * @param {object} doc Document
 */
function limitSearch(query, doc) {
  var state = false;
  if (
    query.date === "" &&
    query.categories.length == 0 &&
    query.tags.length == 0
  ) {
    state = true;
  }
  if (query.date !== "") {
    state = (function(d1, d2) {
      d1 = new Date(d1);
      d2 = new Date(d2);
      return (
        d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate()
      );
    })(query.date, doc.date);
  }
  if (query.categories.length > 0) {
    state = query.categories.every(v => doc.taxonomy.categories.includes(v));
  }
  if (query.tags.length > 0) {
    state = query.tags.every(v => doc.taxonomy.tags.includes(v));
  }
  return state;
}

/**
 * Toggle search results
 * @param {string} target Target element
 * @param {object} sibling Sibling element
 */
function toggleResults(target, sibling) {
  const targetNode = document.querySelector(target);
  const siblingNode = document.querySelector(sibling);
  const searchObserver = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      const value = targetNode.getAttribute(mutation.attributeName);
      if (value == "true") {
        siblingNode.style.setProperty("display", "none");
        targetNode.style.setProperty("display", "block");
      } else if (value == "false") {
        siblingNode.style.setProperty("display", "block");
        targetNode.style.removeProperty("display");
      }
    });
  });
  searchObserver.observe(targetNode, {
    attributes: true,
  });
}

/**
 * Render search results
 * @param {string} target  Target element
 * @param {object} results Search results
 */
function renderResults(target, results) {
  Scholar.accessibilityDestruct(target);
  document.querySelector(target).innerHTML = "";
  if (results.length < 1) {
    const paragraph = document.createElement("p");
    paragraph.appendChild(
      document.createTextNode(ScholarTranslation.SEARCH.EMPTY)
    );
    document.querySelector(target).appendChild(paragraph);
  }
  for (let i = 0; i < results.length; i++) {
    var item = document.querySelector(".search-result-template");
    var template = document.importNode(item.content, true);
    template.querySelector("h3 a").textContent = results[i].title;
    template.querySelector("h3 a").setAttribute("href", results[i].url);
    const paragraph = template.querySelector("p");
    paragraph.appendChild(
      document.createTextNode(ScholarTranslation.GENERIC.AT + " ")
    );
    renderTime(paragraph, results[i].date);
    renderTaxonomy(
      paragraph,
      results[i].taxonomy.categories,
      "category",
      ScholarTranslation.GENERIC.IN
    );
    renderTaxonomy(
      paragraph,
      results[i].taxonomy.tags,
      "tag",
      ScholarTranslation.GENERIC.WITH
    );
    paragraph.appendChild(document.createTextNode("."));
    paragraph.normalize();
    document.querySelector(target).appendChild(template);
  }
  Scholar.accessibilityInit(target);
}

/**
 * Create a localized time-tag
 * @param {HTMLElement} paragraph Reference to p-tag
 * @param {string} data Datetime as string
 */
function renderTime(paragraph, data) {
  const anchor = document.createElement("a");
  anchor.setAttribute(
    "href",
    `${searchRoute}/?date=${dayjs(data).format("YYYY-MM-DD")}`
  );
  const time = document.createElement("time");
  time.setAttribute("datetime", data);
  time.appendChild(
    document.createTextNode(
      dayjs(data).format(toDayJSFormat(systemDateformat.short))
    )
  );
  anchor.appendChild(time);
  paragraph.appendChild(anchor);
}

/**
 * Create a-tags for taxonomy items
 * @param {HTMLElement} paragraph Reference to p-tag
 * @param {string|array} data Page taxonomy
 * @param {string} name Class name
 * @param {string} separator Semantic word
 */
function renderTaxonomy(paragraph, data, name, separator) {
  if (data === "" || data.length < 1) {
    return;
  }
  paragraph.appendChild(document.createTextNode(` ${separator} `));
  if (typeof data === "string" || data instanceof String) {
    if (data.includes(" ")) {
      data = data.split(" ");
    } else {
      data = [data];
    }
  }
  for (let n = 0; n < data.length; n++) {
    const taxonomy = document.createElement("a");
    taxonomy.classList.add(name);
    taxonomy.setAttribute("href", `${searchRoute}/?${name}=${data[n]}`);
    taxonomy.appendChild(document.createTextNode(data[n]));
    if (n > 0 && n < data.length) {
      paragraph.appendChild(document.createTextNode(", "));
    }
    paragraph.appendChild(taxonomy);
  }
}

/**
 * Debounce execution of callback
 * @param {function} func Function to execute
 * @param {int} delay Millisecond delay
 * @see https://codeburst.io/throttling-and-debouncing-in-javascript-b01cad5c8edf
 */
function debounce(func, delay) {
  let inDebounce;
  return function() {
    const context = this;
    const args = arguments;
    clearTimeout(inDebounce);
    inDebounce = setTimeout(() => func.apply(context, args), delay);
  };
}

/**
 * Throttle execution of callback
 * @param {function} func Function to execute
 * @param {int} limit Millisecond limit
 * @see https://codeburst.io/throttling-and-debouncing-in-javascript-b01cad5c8edf
 */
function throttle(func, limit) {
  let lastFunc;
  let lastRan;
  return function() {
    const context = this;
    const args = arguments;
    if (!lastRan) {
      func.apply(context, args);
      lastRan = Date.now();
    } else {
      clearTimeout(lastFunc);
      lastFunc = setTimeout(function() {
        if (Date.now() - lastRan >= limit) {
          func.apply(context, args);
          lastRan = Date.now();
        }
      }, limit - (Date.now() - lastRan));
    }
  };
}

/**
 * Convert PHP date format to Moment date format
 * @param {string} format PHP date format
 */
function toMomentFormat(format) {
  var conversions = {
    d: "DD",
    D: "ddd",
    j: "D",
    l: "dddd",
    N: "E",
    S: "o",
    w: "e",
    z: "DDD",
    W: "W",
    F: "MMMM",
    m: "MM",
    M: "MMM",
    n: "M",
    t: "",
    L: "",
    o: "YYYY",
    Y: "YYYY",
    y: "YY",
    a: "a",
    A: "A",
    B: "",
    g: "h",
    G: "H",
    h: "hh",
    H: "HH",
    i: "mm",
    s: "ss",
    u: "SSS",
    e: "zz",
    I: "",
    O: "",
    P: "",
    T: "",
    Z: "",
    c: "",
    r: "",
    U: "X",
    " ": " ",
  };
  const items = format.split("");
  var momentFormat = "";
  for (let item in items) {
    momentFormat += conversions[items[item]];
  }
  return momentFormat;
}

/**
 * Convert PHP date format to DayJS date format
 * @param {string} format PHP date format
 */
function toDayJSFormat(format) {
  var conversions = {
    d: "DD",
    D: "ddd",
    j: "D",
    l: "dddd",
    N: "",
    S: "o",
    w: "d",
    z: "",
    W: "",
    F: "MMMM",
    m: "MM",
    M: "MMM",
    n: "M",
    t: "",
    L: "",
    o: "YYYY",
    Y: "YYYY",
    y: "YY",
    a: "a",
    A: "A",
    B: "",
    g: "h",
    G: "H",
    h: "hh",
    H: "HH",
    i: "mm",
    s: "ss",
    u: "",
    v: "SSS",
    e: "",
    I: "",
    O: "ZZ",
    P: "Z",
    T: "",
    Z: "",
    c: "",
    r: "",
    U: "X",
    " ": " ",
  };
  const items = format.split("");
  var momentFormat = "";
  for (let item in items) {
    momentFormat += conversions[items[item]];
  }
  return momentFormat;
}

/**
 * Convert milliseconds to human readable time
 * @param {number} millisec Float containing milliseconds
 * @see https://stackoverflow.com/a/32180863
 */
function msToTime(millisec) {
  var milliseconds = millisec.toFixed(2);
  var seconds = (millisec / 1000).toFixed(1);
  var minutes = (millisec / (1000 * 60)).toFixed(1);
  var hours = (millisec / (1000 * 60 * 60)).toFixed(1);
  var days = (millisec / (1000 * 60 * 60 * 24)).toFixed(1);
  if (seconds <= 0) {
    return milliseconds + " ms";
  } else if (seconds < 60) {
    return seconds + " sec";
  } else if (minutes < 60) {
    return minutes + " min";
  } else if (hours < 24) {
    return hours + " hrs";
  } else {
    return days + " days";
  }
}

export { searchFieldInit, searchPageInit };
