/// <reference types="Cypress" />

describe(`Contrast at ${"docs"} (${"/docs"})`, () => {
  beforeEach(function() {
    cy.visit("/docs/security/overview");
  });
  it("Tabs correctly", function() {
    cy.get('body').tab();
    // cy.log(cy.focused());
    // cy.log(cy.focused()[0].ownerDocument.nodeName);
    // while (true) {
    //     //your code
    // }
    /* for (let i = 0; i < 5; i++) {
      cy.tab();
      // cy.log(cy.focused());
      // cy.focused();
      cy.focused().then((element) => {
        // if (typeof element.ownerDocument !== 'undefined') {
        //   cy.log(element.ownerDocument);
        //   // cy.log(element.ownerDocument.nodeName);
        // }
        cy.log(element[0].ownerDocument.nodeName);
      })
    } */
  });
});
