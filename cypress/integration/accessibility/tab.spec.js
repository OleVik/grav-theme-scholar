/// <reference types="Cypress" />

var focusableElements;

for (const [index, route] of Object.entries(Cypress.env("routes"))) {
  describe(`Tab-behavior at ${index} ${route}`, () => {
    before("Has tabbable elements", function() {
      cy.visit(route);
      cy.document().then(doc => {
        focusableElements = doc.querySelectorAll(Cypress.env("tabElements"));
      });
    });
    it("Assigns indices to tabbable elements", function() {
      expect(focusableElements.length).to.be.at.least(1);
      cy.document().then(doc => {
        for (let i = 0; i < focusableElements.length; i++) {
          focusableElements[i].style.backgroundColor = "rgba(255,0,0,0.25)";
          const rect = focusableElements[i].getBoundingClientRect();
          var tip = doc.createElement("div");
          tip.innerHTML = i + 1;
          let style = `height:1.25rem;width:1.25rem;background:red;position:absolute;line-height:1;font-size:1.25rem;;top:${
            rect.top
          }px;left:${rect.left + rect.width}px;`;
          tip.setAttribute("style", style);
          doc.body.style.position = "relative";
          doc.body.appendChild(tip);
        }
      });
    });
  });
}
