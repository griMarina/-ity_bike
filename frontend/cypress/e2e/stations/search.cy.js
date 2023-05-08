describe("Search functionality", () => {
  beforeEach(() => {
    cy.visit("#/stations");
  });

  it("searches for a station by name and displays correct results", () => {
    cy.get("[role=search]").type("Kaivopuisto");
    cy.get("table tbody tr").should("have.length", 1);
    cy.get("[role=name]").contains("Kaivopuisto");
  });

  it("searches for a station by address and displays correct results", () => {
    cy.get("[role=search]").type("Meritori 1");
    cy.get("table tbody tr").should("have.length", 1);
    cy.get("[role=address]").contains("Meritori 1");
  });

  it("displays no results for non-existent search query", () => {
    cy.get("[role=search]").type("Non-existent Station");
    cy.get("[role=status]").contains("No stations found");
  });
});
