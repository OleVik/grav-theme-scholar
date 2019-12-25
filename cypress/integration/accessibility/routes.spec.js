/// <reference types="Cypress" />

for (const [index, route] of Object.entries(Cypress.env("routes"))) {
  for (const [name, dynamicRoute] of Object.entries(
    Cypress.env("dynamicRoutes")
  )) {
    describe(`${index}: ${name}`, () => {
      it(`Visits ${dynamicRoute} (${route})`, function() {
        if (route == "/") {
          if (name == "data") {
            cy.request(dynamicRoute);
          } else {
            cy.visit(dynamicRoute);
          }
        } else {
          if (name == "data") {
            cy.request(`${route}${dynamicRoute}`);
          } else {
            cy.visit(`${route}${dynamicRoute}`);
          }
        }
      });
    });
  }
}
