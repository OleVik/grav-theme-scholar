/// <reference types="Cypress" />

for (const [index, route] of Object.entries(Cypress.env("routes"))) {
  if (["index", "blog", "docs"].includes(index)) {
    describe(`Index-search at ${index} ${route}/search`, () => {
      before(function() {
        cy.visit(route);
      });
        it("Accepts input", function() {
          cy.get("#query").type("site");
        });
        it("Toggles results", function() {
          cy.get(".search-results").should('be.visible');
        });
    });
  }
  describe(`Content-search at ${index} ${route}/search`, () => {
    before(function() {
      if (route == "/") {
        cy.visit(`/search`);
      } else {
        cy.visit(`${route}/search`);
      }
      cy.injectAxe();
    });
    it("Has no ARIA violations", function() {
      cy.checkA11y(Cypress.env("context"), Cypress.env("config"));
    });
    it("Accepts input", function() {
      cy.get(".search-query #title").type("site");
    });
    it("Toggles results", function() {
      cy.get(".search-results").should('be.visible');
    });
  });
}
