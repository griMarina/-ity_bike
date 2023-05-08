describe("Sorting functionality", () => {
  beforeEach(() => {
    cy.visit("#/stations");
  });

  it("sorts the Station list by id when the user selects the 'id' option from the dropdown", () => {
    cy.sortByNum("id");
  });

  it("sorts the Station list by name when the user selects the 'name' option from the dropdown", () => {
    cy.sortByString("name");
  });

  it("sorts the Station list by address when the user selects the 'address' option from the dropdown", () => {
    cy.sortByString("address");
  });

  it("sorts the Station list by capacity when the user selects the 'capacity' option from the dropdown", () => {
    cy.sortByNum("capacity");
  });
});
