/// <reference types="Cypress" />

describe("Accessibility: " + "/", () => {
  before(function() {
    cy.visit("/");
    cy.injectAxe();
  });
  it("/" + " has no violations", function() {
    cy.checkA11y();
  });
  it("/" + " has no active violations", function() {
    Cypress.env("testElementsIndex").forEach(element => {
      cy.get(element).invoke("attr", "class", "active");
    });
    cy.get('header[role="banner"] .menu .search input[type="search"]')
      .focus()
      .type("Hello World");
    cy.checkA11y();
  });
  /* Cypress.env("styles").forEach(style => {
      it(style + " has sufficient contrast", function() {
        cy.document().then(doc => {
          if (doc.querySelector("link[href$='metal.css']")) {
            let cssLink = doc.querySelector("link[href$='metal.css']");
            // let cssLink = doc.querySelector(
            //   "link[href^='" + Cypress.env("stylesFolder") + "']"
            // );
            let cssPath = cssLink.getAttribute("href").replace("metal.css", "");
            cssLink.setAttribute("href", cssPath + style);
          }
        });
        cy.checkA11y({
          runOnly: ["cat.color"],
        });
        // cy.screenshot(route + "/styles/" + style);
      });
    }); */
});
