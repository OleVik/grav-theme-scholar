/**
 * Initialize mobile helpers
 */
function init() {
  const main = document.querySelector("main");
  const sidebar = document.querySelector("aside.sidebar");
  const links = document.querySelector("nav[role='navigation'].links");
  if (main && sidebar) {
    const sidebarObserver = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type == "attributes") {
          if (mutation.target.getAttribute("aria-checked") === "true") {
            main.style.display = "none";
            mutation.target.style.display = "flex";
          } else if (mutation.target.getAttribute("aria-checked") === "false") {
            mutation.target.style.display = "none";
            main.style.display = "flex";
          }
        }
      });
    });
    sidebarObserver.observe(sidebar, {
      attributes: true,
    });
  }
  if (links) {
    const linksObserver = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type == "attributes") {
          if (mutation.target.getAttribute("aria-checked") === "true") {
            links.style.display = "block";
          } else if (mutation.target.getAttribute("aria-checked") === "false") {
            links.style.display = "none";
          }
        }
      });
    });
    linksObserver.observe(links, {
      attributes: true,
    });
  }
}

export { init as mobileInit };
