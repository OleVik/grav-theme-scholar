/// <reference types="Cypress" />

for (const [breakpoint, width] of Object.entries(Cypress.env("breakpoints"))) {
  for (const [index, route] of Object.entries(Cypress.env("routes"))) {
    describe(`Screenshot ${index} ${route}`, function() {
      before(function() {
        cy.visit(route);
        cy.viewport(width, 800);
      });
      it(`Captures breakpoint: ${breakpoint}`, function() {
        cy.get("body")
          .first()
          .screenshot(`${width}/${index}`, {
            clip: {
              x: 0,
              y: 0,
              width: width,
              height: 800,
            },
          });
      });
    });
  }
}
