describe("Trip list", () => {
  it("navigates to correct station page when the user clicks on departure station name", () => {
    cy.visit("#/trips");
    cy.wait(1000);

    cy.get("[role=departure]").contains("Töölöntulli").click();

    cy.url().should("include", `/stations/82`);

    cy.get(".station__info").should("exist");
    cy.get("[role=name]").should("contain", "Töölöntulli");
  });

  it("navigates to correct station page when the user clicks on return station name", () => {
    cy.visit("#/trips");
    cy.wait(1000);

    cy.get("[role=return]").contains("Pasilan asema").click();

    cy.url().should("include", `/stations/113`);

    cy.get(".station__info").should("exist");
    cy.get("[role=name]").should("contain", "Pasilan asema");
  });
});
