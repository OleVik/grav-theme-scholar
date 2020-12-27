/// <reference types="Cypress" />

for (const [index, route] of Object.entries(Cypress.env("routes"))) {
  for (const [name, dynamicRoute] of Object.entries(
    Cypress.env("dynamicRoutes")
  )) {
    var base = "";
    if (route !== "/") {
      base = route;
    }
    describe(`${index}: ${name}`, () => {
      it(`Reaches ${base}${dynamicRoute}`, function() {
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
