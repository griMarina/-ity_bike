describe("Home", () => {
  it("loads the home page correctly", () => {
    cy.visit("/");
    cy.title().should("contain", "Helsinki City Bike");
    cy.get("[role=header]").should(
      "contain",
      "Welcome to Helsinki City Bike App!"
    );
  });
});
