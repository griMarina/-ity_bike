describe("Pagination functionality", () => {
  beforeEach(() => {
    cy.visit("#/stations");
  });

  it("shows the first 30 rows on initial page load", () => {
    cy.get("[role=id]").should("have.length", 30);
    cy.get("[role=id]").eq(0).should("contain", "1");
    cy.get("[role=id]").eq(29).should("contain", "30");
  });

  it("shows the next 30 rows when 'Next' pagination button is clicked", () => {
    cy.get("[role=button-next]").click();
    cy.get("table tbody tr").should("have.length", 30);
    cy.get("[role=id]").eq(0).should("contain", "31");
    cy.get("[role=id]").eq(29).should("contain", "60");
  });

  it("shows the previous 30 rows when 'Prev' pagination button is clicked", () => {
    cy.get("[role=button-next]").click();
    cy.get("[role=button-previous]").click();
    cy.get("[role=id]").should("have.length", 30);
    cy.get("[role=id]").eq(0).should("contain", "1");
    cy.get("[role=id]").eq(29).should("contain", "30");
  });

  it("shows the remaining rows on last page when '>>' pagination button is clicked", () => {
    cy.get("[role=button-last]").click();
    cy.wait(1000);
    cy.get("[role=id]").should("have.length", 7);
    cy.get("[role=id]").eq(0).should("contain", "761");
    cy.get("[role=id]").eq(6).should("contain", "902");
  });

  it("should show the first 30 rows when '<<' pagination button is clicked", () => {
    cy.get("[role=button-last]").click();
    cy.get("[role=button-first]").click();
    cy.get("[role=id]").should("have.length", 30);
    cy.get("[role=id]").eq(0).should("contain", "1");
    cy.get("[role=id]").eq(29).should("contain", "30");
  });
});
