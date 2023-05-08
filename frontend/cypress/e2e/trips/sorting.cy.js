describe("Sorting functionality", () => {
  beforeEach(() => {
    cy.visit("#/trips");
  });

  it("sorts the Trip list by departure station name when the user selects the 'departure station' option from the dropdown", () => {
    cy.sortByString("departure");
  });

  it("sorts the Trip list by return station name when the user selects the 'return station' option from the dropdown", () => {
    cy.sortByString("return");
  });

  it("sorts the Trip list by distance when the user selects the 'distance' option from the dropdown", () => {
    cy.sortByNum("distance");
  });

  it("sorts the Trip list by duration when the user selects the 'duration' option from the dropdown", () => {
    cy.sortByNum("duration");
  });
});
