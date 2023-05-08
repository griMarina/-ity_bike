describe("Navigation bar", () => {
  beforeEach(() => {
    cy.visit("/");
  });

  it("navigates to Stations page on clicking the Station link", () => {
    cy.get("nav").contains("Stations").click();
    cy.url().should("include", "/stations");
    cy.get("[role=header]").contains("City Bike Stations");
  });

  it("navigates to Trips page on clicking the Trips link", () => {
    cy.get("nav").contains("Trips").click();
    cy.url().should("include", "/trips");
    cy.get("[role=header]").contains("City Bike Trips");
  });
});
