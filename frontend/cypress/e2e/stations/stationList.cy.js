describe("Station list", () => {
  it("navigates to correct station page when the user clicks on station name", () => {
    cy.visit("#/stations");

    cy.get("[role=name]").contains("Laivasillankatu").click();

    cy.url().should("include", `/stations/2`);

    cy.get(".station__info").should("exist");
    cy.get("[role=name]").should("contain", "Laivasillankatu");
  });
});
