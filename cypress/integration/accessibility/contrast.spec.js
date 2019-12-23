/// <reference types="Cypress" />

for (const [index, route] of Object.entries(Cypress.env("routes"))) {
  describe(`Contrast at ${index} (${route})`, () => {
    it(index + " has active elements", function() {
      expect(Object.keys(Cypress.env("activeElements"))).to.include(index);
      expect(Cypress.env("activeElements")[index].length).to.be.at.least(1);
    });
    Cypress.env("styles").forEach(style => {
      style = style.replace(".css", "");
      style = style.charAt(0).toUpperCase() + style.slice(1);
      it(style + " has sufficient contrast", function() {
        cy.visit(`${route}?theme=${style}`);
        cy.injectAxe();
        const config = Cypress.env("config");
        config.runOnly = ["cat.color"];
        cy.checkA11y(Cypress.env("context"), config);
      });
      if (
        index in Cypress.env("activeElements") &&
        Cypress.env("activeElements")[index].length > 0
      ) {
        it(style + " has sufficient active contrast", function() {
          cy.visit(`${route}?theme=${style}`);
          cy.injectAxe();
          Cypress.env("activeElements")[index].forEach(element => {
            cy.get(element).invoke("attr", "class", "active");
          });
          const config = Cypress.env("config");
          config.runOnly = ["cat.color"];
          cy.checkA11y(Cypress.env("context"), config);
        });
      }
    });
  });
}
