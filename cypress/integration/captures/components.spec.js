/// <reference types="Cypress" />

for (const [index, route] of Object.entries(Cypress.env("routes"))) {
  describe(`Screenshot ${index} ${route}`, function() {
    if (Object.keys(Cypress.env("landmarks")).includes(index)) {
      before(function() {
        cy.visit(route);
        cy.viewport(1000, 800);
      });
      for (const [key, selector] of Object.entries(
        Cypress.env("landmarks")[index]
      )) {
        it(`Captures landmark: ${key}`, function() {
          cy.get(selector)
            .first()
            .screenshot(`${index}/${key}`, {
              clip: {
                x: 0,
                y: 0,
                width: 1000,
                height: 800,
              },
            });
        });
      }
    }
  });
}
