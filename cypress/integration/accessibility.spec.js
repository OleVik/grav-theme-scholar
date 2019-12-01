/// <reference types="Cypress" />

Cypress.env("routes").forEach(route => {
  describe("Accessibility: " + route, () => {
    before(function() {
      cy.visit(route);
      cy.configureAxe({
        exclude: [["#query"], [".search-button"]],
      });
      cy.injectAxe();
    });
    it(route + " has no violations", function() {
      cy.checkA11y();
    });
    it(route + " has no active violations", function() {
      cy.get('header[role="banner"] h1 a').trigger("mouseover");
      cy.get('header[role="banner"] .links a:first-of-type').trigger(
      cy.get('aside article:first-of-type header a:first-of-type').trigger("mouseover");
      cy.get('aside article:first-of-type footer a:first-of-type').trigger("mouseover");
      cy.get('header[role="banner"] .menu .search-button').trigger(
        "click"
      );
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
});
