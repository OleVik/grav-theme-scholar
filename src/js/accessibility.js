/**
 * Initialize accessibility helpers
 */
function init(prefix = "") {
  const buttons = document.querySelectorAll(`${prefix} [role="button"]`);
  for (var i = 0; i < buttons.length; ++i) {
    if ("target" in buttons[i].dataset) {
      buttons[i].addEventListener("click", toggleHandler);
      buttons[i].addEventListener("keydown", toggleHandler);
      buttons[i].addEventListener("keyup", toggleHandler);
    } else {
      buttons[i].addEventListener("click", buttonHandler);
      buttons[i].addEventListener("keydown", buttonHandler);
      buttons[i].addEventListener("keyup", buttonHandler);
    }
  }
  const links = document.querySelectorAll(`${prefix} a[href]`);
  for (var i = 0; i < links.length; ++i) {
    links[i].addEventListener("click", buttonHandler);
    links[i].addEventListener("keydown", buttonHandler);
    links[i].addEventListener("keyup", buttonHandler);
  }
  if (
    document.querySelector(".next-previous") &&
    document.querySelector("body").classList.contains("docs")
  ) {
    window.addEventListener(
      "keydown",
      function(event) {
        switch (event.code) {
          case "ArrowLeft":
            if (
              document.querySelector(".next-previous article:first-of-type a")
            ) {
              document
                .querySelector(".next-previous article:first-of-type a")
                .click();
            }
            break;
          case "ArrowRight":
            if (
              document.querySelector(".next-previous article:last-of-type a")
            ) {
              document
                .querySelector(".next-previous article:last-of-type a")
                .click();
            }
            break;
        }
      },
      true
    );
  }
}

function destruct(prefix = "") {
  const buttons = document.querySelectorAll(`${prefix} [role="button"]`);
  for (var i = 0; i < buttons.length; ++i) {
    if ("target" in buttons[i].dataset) {
      buttons[i].removeEventListener("click", toggleHandler);
      buttons[i].removeEventListener("keydown", toggleHandler);
      buttons[i].removeEventListener("keyup", toggleHandler);
    } else {
      buttons[i].removeEventListener("click", buttonHandler);
      buttons[i].removeEventListener("keydown", buttonHandler);
      buttons[i].removeEventListener("keyup", buttonHandler);
    }
  }
  const links = document.querySelectorAll(`${prefix} a[href]`);
  for (var i = 0; i < links.length; ++i) {
    links[i].removeEventListener("click", buttonHandler);
    links[i].removeEventListener("keydown", buttonHandler);
    links[i].removeEventListener("keyup", buttonHandler);
  }
}

/**
 * @param {MouseEvent|KeyboardEvent} event
 */
function buttonHandler(event) {
  if (event.target.hasAttribute("href")) {
    switch (event.code) {
      case "Space":
        event.target.click();
        window.location = event.target.getAttribute("href");
        break;
      case "Enter":
        event.target.click();
        window.location = event.target.getAttribute("href");
        break;
    }
  }
}

/**
 * @param {MouseEvent|KeyboardEvent} event
 */
function toggleHandler(event) {
  const targetElement = document.querySelector(event.target.dataset.target);
  if (event.type === "click") {
    toggleButtonState(targetElement);
  } else if (event.type === "keydown") {
    switch (event.code) {
      case "Space":
        toggleButtonState(targetElement);
        break;
      case "Enter":
        toggleButtonState(targetElement);
        break;
      case "Escape":
        toggleButtonState(targetElement, "false");
        break;
    }
  }
}

/**
 * Toggles the button's target's state
 *
 * @param {HTMLElement} targetElement
 * @param {Boolean} force
 */
function toggleButtonState(targetElement, force = null) {
  if (targetElement.hasAttribute("aria-checked")) {
    if (force) {
      targetElement.setAttribute("aria-checked", force);
      return;
    }
    if (targetElement.getAttribute("aria-checked") === "true") {
      targetElement.setAttribute("aria-checked", "false");
    } else if (targetElement.getAttribute("aria-checked") === "false") {
      targetElement.setAttribute("aria-checked", "true");
    }
  }
  if (targetElement.hasAttribute("aria-expanded")) {
    if (force) {
      targetElement.setAttribute("aria-expanded", force);
      return;
    }
    if (targetElement.getAttribute("aria-expanded") === "true") {
      targetElement.setAttribute("aria-expanded", "false");
    } else if (targetElement.getAttribute("aria-expanded") === "false") {
      targetElement.setAttribute("aria-expanded", "true");
    }
  }
  if (targetElement.hasAttribute("aria-pressed")) {
    if (force) {
      targetElement.setAttribute("aria-pressed", force);
      return;
    }
    if (targetElement.getAttribute("aria-pressed") === "true") {
      targetElement.setAttribute("aria-pressed", "false");
    } else if (targetElement.getAttribute("aria-pressed") === "false") {
      targetElement.setAttribute("aria-pressed", "true");
    }
  }
}

export {
  init as accessibilityInit,
  destruct as accessibilityDestruct,
  buttonHandler,
  toggleHandler,
  toggleButtonState,
};
