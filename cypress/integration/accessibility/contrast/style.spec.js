/// <reference types="Cypress" />

describe(`Contrast at ${"docs"} (${"/docs"})`, () => {
  beforeEach(function() {
    cy.visit("/docs/security/overview?theme=wine");
    cy.injectAxe();
  });
  it("Style has sufficient contrast", function() {
    const config = Cypress.env("config");
    config.runOnly = ["cat.color"];
    cy.checkA11y(Cypress.env("context"), config);
  });
  it("Style has sufficient active contrast", function() {
    cy.get("a").invoke("attr", "class", "active");
    const config = Cypress.env("config");
    config.runOnly = ["cat.color"];
    cy.checkA11y(Cypress.env("context"), config);
  });
});
