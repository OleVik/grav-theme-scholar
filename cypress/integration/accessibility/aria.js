/// <reference types="Cypress" />

for (const [index, route] of Object.entries(Cypress.env("routes"))) {
  describe(`ARIA at ${index} (${route})`, () => {
    before(function() {
      cy.visit(route);
      cy.injectAxe();
    });
    it("Has no violations", function() {
      cy.checkA11y(Cypress.env("context"), Cypress.env("config"));
    });
  });
}
