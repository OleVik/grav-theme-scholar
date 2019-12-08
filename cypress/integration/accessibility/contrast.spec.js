/// <reference types="Cypress" />

// cy.get('header[role="banner"] .menu .search input[type="search"]')
//   .focus()
//   .type("Hello World");
// cy.screenshot(route + "/styles/" + style);

for (const [index, route] of Object.entries(Cypress.env("routes"))) {
  describe(`Contrast at ${index} (${route})`, () => {
    beforeEach(function() {
      cy.visit(route);
      cy.injectAxe();
    });
    it(index + " has active elements", function() {
      expect(Object.keys(Cypress.env("elements"))).to.include(index);
      expect(Cypress.env("elements")[index].length).to.be.at.least(1);
    });
    Cypress.env("styles").forEach(style => {
      it(style + " has sufficient contrast", function() {
        cy.document().then(doc => {
          let cssLink = doc.querySelector(
            "link[href^='/user/themes/scholar/css/styles/']"
          );
          if (cssLink) {
            cssLink.setAttribute(
              "href",
              "/user/themes/scholar/css/styles/" + style
            );
            doc.querySelector("title").innerHTML = style;
          }
        });
        const config = Cypress.env("config");
        config.runOnly = ["cat.color"];
        cy.checkA11y(Cypress.env("context"), config);
      });
      if (
        index in Cypress.env("elements") &&
        Cypress.env("elements")[index].length > 0
      ) {
        it(style + " has sufficient active contrast", function() {
          cy.document().then(doc => {
            let cssLink = doc.querySelector(
              "link[href^='/user/themes/scholar/css/styles/']"
            );
            if (cssLink) {
              cssLink.setAttribute(
                "href",
                "/user/themes/scholar/css/styles/" + style
              );
              doc.querySelector("title").innerHTML = style;
            }
          });
          Cypress.env("elements")[index].forEach(element => {
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
