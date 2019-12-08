/*
 * TinyDrawer.js
 * Modified for the Scholar Theme
 * @see https://github.com/jenstornell/tinyDrawer.js
 */
class TinyDrawer {
  constructor(options) {
    this.o = Object.assign({}, this.defaults(), options);
  }

  load() {
    document.addEventListener("DOMContentLoaded", () => {
      this.setup();
    });
  }

  defaults() {
    return {
      replacement: "drawer",
      drawerSelector: "drawer-menu",
    };
  }

  setup() {
    this.elementDrawer = document.querySelector(this.o.drawerSelector);
    if (
      !this.elementDrawer &&
      this.elementDrawer.nodeType !== Node.ELEMENT_NODE
    ) {
      return;
    }
    this.backdropAdd();
    this.activeUnset();
    this.elementOpen = document.querySelectorAll(
      "[data-" + this.o.replacement + "-open]"
    );
    this.elementClose = document.querySelectorAll(
      "[data-" +
        this.o.replacement +
        "-backdrop], [data-" +
        this.o.replacement +
        "-close]"
    );
    window.addEventListener(
      "keydown",
      function(event) {
        switch (event.code) {
          case "Escape":
            if (
              Scholar.tinyDrawerObj.elementDrawer.getAttribute(
                "aria-checked"
              ) === "true"
            ) {
              Scholar.toggleButtonState(
                Scholar.tinyDrawerObj.elementDrawer,
                "false"
              );
            }
            break;
        }
      },
      true
    );
    this.elementClose.forEach(item => {
      item.addEventListener(
        "click",
        function(event) {
          if (
            Scholar.tinyDrawerObj.elementDrawer.getAttribute("aria-checked") ===
            "true"
          ) {
            Scholar.toggleButtonState(
              Scholar.tinyDrawerObj.elementDrawer,
              "false"
            );
          }
        },
        true
      );
    });
    this.observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type == "attributes") {
          if (mutation.target.getAttribute("aria-checked") === "true") {
            Scholar.tinyDrawerOpen(mutation.target);
          } else if (mutation.target.getAttribute("aria-checked") === "false") {
            Scholar.tinyDrawerClose(mutation.target);
          }
        }
      });
    });
    this.observer.observe(this.elementDrawer, {
      attributes: true,
    });
  }

  activeUnset() {
    document.body.dataset[this.o.replacement] = "";
  }

  activeSet() {
    document.body.dataset[this.o.replacement] = true;
  }

  offsetTopToVariable() {
    var offsets = document.body.getBoundingClientRect();
    this.top = -offsets.top;
  }

  open(element = null) {
    this.activeSet();
    this.offsetTopToVariable();
    this.callback(element, "open");
    document
      .querySelector(".drawer .drawer-inner")
      .setAttribute("aria-expanded", "true");
  }

  close(element = null) {
    this.activeUnset();
    window.scrollTo(0, Math.abs(document.body.getBoundingClientRect().top));
    this.callback(element, "close");
    document
      .querySelector(".drawer .drawer-inner")
      .setAttribute("aria-expanded", "false");
  }

  backdropAdd() {
    let backdrop = document.createElement("div");
    backdrop.dataset[this.o.replacement + "Backdrop"] = "";
    document.body.appendChild(backdrop);
    this.elementBackdrop = document.querySelector(
      "[data-" + this.o.replacement + "-backdrop]"
    );
  }

  callback(element, action) {
    if (typeof this.o.callback == "undefined") return;
    this.o.callback(element, action);
  }
}

var tinyDrawerObj;

function init(options = null) {
  tinyDrawerObj = new TinyDrawer(options);
  tinyDrawerObj.load();
  return tinyDrawerObj;
}

function tinyDrawerOpen(e = null) {
  let target = e ? e.target : null;
  tinyDrawerObj.open(target);
}

function tinyDrawerClose(e = null) {
  let target = e ? e.target : null;
  tinyDrawerObj.close(target);
}

export { init as drawerInit, tinyDrawerObj, tinyDrawerOpen, tinyDrawerClose };
