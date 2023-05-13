describe("Station page view", () => {
  function getStationInfo() {
    cy.intercept("GET", "http://localhost:8080/station/show?id=1", {
      fixture: "station.json",
    }).as("getStationInfo");

    cy.visit("#/stations/1");
    cy.wait("@getStationInfo");
  }

  it("displays station information", () => {
    getStationInfo();

    cy.fixture("station.json").then((data) => {
      const station = data.data;

      cy.get("[role=name]").should("contain", station.name);
      cy.get("[role=address]").should("contain", station.address);
      cy.get("[role=capacity]").should("contain", station.capacity);
      cy.get("[role=total_trips_start]").should("contain", station.total_start);
      cy.get("[role=total_trips_end]").should("contain", station.total_end);

      const avgDistanceStart = (station.avg_distance_start / 1000).toFixed(2);
      const avgDistanceEnd = (station.avg_distance_end / 1000).toFixed(2);

      cy.get("[role=average_distance_start]").should(
        "contain",
        avgDistanceStart
      );
      cy.get("[role=average_distance_end]").should("contain", avgDistanceEnd);
    });
  });

  it("displays station location on the map", () => {
    getStationInfo();

    cy.window().then((win) => {
      const map = win.map;
      const center = map.getCenter();

      cy.fixture("station.json").then((data) => {
        const station = data.data;

        expect(center.lat).to.be.closeTo(station.y, 0.001);
        expect(center.lng).to.be.closeTo(station.x, 0.001);
      });
    });
  });

  it("displays an error message for invalid station ID", () => {
    cy.visit("#/stations/invalid_id");
    cy.get("[role=status]", { timeout: 5000 }).should(
      "contain",
      "Invalid station id"
    );
  });

  it("displays an error message for non-existing station", () => {
    cy.visit("#/stations/1000");
    cy.get("[role=status]", { timeout: 5000 }).should(
      "contain",
      "Cannot find station: 1000."
    );
  });
});
