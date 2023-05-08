describe("Search functionality", () => {
  beforeEach(() => {
    cy.visit("#/trips");
  });

  it("searches for a trip by departure station name and displays correct results", () => {
    cy.get("[role=search]").type("Laajalahden aukio");
    cy.get("table tbody tr").should("have.length", 1);
    cy.get("[role=departure]").contains("Laajalahden aukio");
  });

  it("searches for a trip by return station name and displays correct results", () => {
    cy.get("[role=search]").type("Teljäntie");
    cy.get("table tbody tr").should("have.length", 1);
    cy.get("[role=return]").contains("Teljäntie");
  });

  it("displays no results for non-existent search query", () => {
    cy.get("[role=search]").type("Non-existent Trip");
    cy.get("[role=status]").contains("No trips found");
  });
});
