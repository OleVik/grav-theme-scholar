/// <reference types="Cypress" />

Cypress.env("styles").forEach(style => {
  style = style.replace(".css", "");
  for (const [index, route] of Object.entries(Cypress.env("routes"))) {
    describe(`Screenshot (${style}) ${index} ${route}`, function() {
      if (Object.keys(Cypress.env("landmarks")).includes(index)) {
        before(function() {
          cy.visit(`${route}?theme=${style}`);
          cy.viewport(1000, 800);
        });
        for (const [key, selector] of Object.entries(
          Cypress.env("landmarks")[index]
        )) {
          it(`Captures landmark: ${key}`, function() {
            cy.document().then(doc => {
              if (doc.querySelector(selector).hasAttribute("aria-checked")) {
                doc.querySelector(selector).setAttribute("aria-checked", true);
              }
            });
            cy.get(selector)
              .first()
              .screenshot(`${style}/${index}/${key}`, {
                clip: {
                  x: 0,
                  y: 0,
                  width: 1000,
                  height: 800,
                },
              });
            cy.document().then(doc => {
              if (doc.querySelector(selector).hasAttribute("aria-checked")) {
                doc.querySelector(selector).setAttribute("aria-checked", false);
              }
            });
          });
        }
      }
    });
  }
});
