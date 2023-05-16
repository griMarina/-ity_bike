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
  // Select the specified option from the select element
  cy.get("[role=select]").select(option);

  cy.wait(1000);

  // Get all the items with the specified role
  cy.get(`[role=${option}]`).then((items) => {
    // Get the text content of each item
    const itemKeys = items.toArray().map((el) => el.textContent);
    // Sort the item keys in alphabetical order
    const sortedKeys = itemKeys.sort((a, b) => a.localeCompare(b));

    // Assert that the item keys are in the expected sorted order
    expect(itemKeys).to.deep.equal(sortedKeys);
  });
});

Cypress.Commands.add("sortByNum", (option) => {
  cy.get("[role=select]").select(option);

  cy.wait(1000);

  cy.get(`[role=${option}]`).then((items) => {
    const itemKeys = items.toArray().map((el) => parseInt(el.textContent));
    const sortedKeys = [...items]
      .map((el) => parseInt(el.textContent)) // Get the numeric value of each item for sorting
      .sort((a, b) => a - b); // Sort the item keys in ascending order

    expect(itemKeys).to.deep.equal(sortedKeys);
  });
});
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
