/// <reference types="Cypress" />

// cy.get('header[role="banner"] .menu .search input[type="search"]')
//   .focus()
//   .type("Hello World");
// cy.screenshot("/docs" + "/styles/" + style);

describe(`Contrast at ${"docs"} (${"/docs"})`, () => {
  beforeEach(function() {
    cy.visit("/docs/security/overview?theme=amber");
    cy.injectAxe();
  });
  it("amber.css" + " has sufficient contrast", function() {
    // cy.document().then(doc => {
    //   let cssLink = doc.querySelector(
    //     "link[href^='/user/themes/scholar/css/styles/']"
    //   );
    //   if (cssLink) {
    //     cssLink.setAttribute(
    //       "href",
    //       "/user/themes/scholar/css/styles/" + "amber.css"
    //     );
    //     doc.querySelector("title").innerHTML = "amber.css";
    //   }
    // });
    const config = Cypress.env("config");
    config.runOnly = ["cat.color"];
    cy.checkA11y(Cypress.env("context"), config);
  });
  it("amber.css" + " has sufficient active contrast", function() {
    // cy.document().then(doc => {
    //   let cssLink = doc.querySelector(
    //     "link[href^='/user/themes/scholar/css/styles/']"
    //   );
    //   if (cssLink) {
    //     cssLink.setAttribute(
    //       "href",
    //       "/user/themes/scholar/css/styles/" + "amber.css"
    //     );
    //     doc.querySelector("title").innerHTML = "amber.css";
    //   }
    // });
    cy.get("a").invoke("attr", "class", "active");
    const config = Cypress.env("config");
    config.runOnly = ["cat.color"];
    cy.checkA11y(Cypress.env("context"), config);
  });
});
