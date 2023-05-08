// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
Cypress.Commands.add("sortByString", (option) => {
  cy.get("[role=select]").select(option);

  cy.get(`[role=${option}]`).then((items) => {
    const itemKeys = items.toArray().map((el) => el.textContent);
    const sortedKeys = [...itemKeys].sort();

    expect(itemKeys).to.deep.equal(sortedKeys);
  });
});

Cypress.Commands.add("sortByNum", (option) => {
  cy.get("[role=select]").select(option);

  cy.get(`[role=${option}]`).then((items) => {
    const itemKeys = items.toArray().map((el) => parseInt(el.textContent));
    const sortedKeys = [...items]
      .map((el) => parseInt(el.textContent))
      .sort((a, b) => a - b);

    expect(itemKeys).to.deep.equal(sortedKeys);
  });
}); //
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
