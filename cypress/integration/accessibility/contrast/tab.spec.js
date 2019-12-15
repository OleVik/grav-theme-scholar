/// <reference types="Cypress" />

let focusableElements;
let current;

describe(`Tab-behavior at ${"docs"} (${"/docs"})`, () => {
  before(function() {
    cy.visit("/docs/security/overview");
    cy.get("body");
    cy.document().then(doc => {
      focusableElements = doc.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      );
      // Cypress.env(
      //   "focusableElements",
      //   doc.querySelectorAll(
      //     'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      //   )
      // );
    });
    cy.log(focusableElements);
    // cy.log(Cypress.env("focusableElements"));
  });
  it(`Doesnt act like a cunt`, function() {
    cy.log(focusableElements);
  });
  for (let i = 0; i < focusableElements.length; i++) {
    it(`Element ${i}/${focusableElements.length}`, function() {
      // state = `Element ${i}/${Cypress.env("focusableElements").length}`;
      // cy.tab().invoke("attr", "class", "active");
      // cy.tab().invoke("attr", "style", "background-color: rgba(255,0,0,0.25);");
      cy.tab().then(element => {
        cy.log(element);
        current = element;
      });
      cy.log(current);

      // cy.document().then(doc => {
      //   new LeaderLine(current, doc.querySelectorAll("a")[3]);
      // });
    });
  }
});
