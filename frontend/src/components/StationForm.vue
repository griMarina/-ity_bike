<template>
  <div>
    <form @submit.prevent="createStation">
      <h4>Station creation</h4>
      <my-input
        v-focus
        v-model="station.name_fi"
        type="text"
        placeholder="name (finnish)"
        required
      />
      <my-input
        v-model="station.name_sv"
        type="text"
        placeholder="name (swedish)"
        required
      />
      <my-input
        v-model="station.name_en"
        type="text"
        placeholder="name (english)"
        required
      />
      <my-input
        v-model="station.address_fi"
        type="text"
        placeholder="address (finnish)"
        required
      />
      <my-input
        v-model="station.address_sv"
        type="text"
        placeholder="address (swedish)"
        required
      />
      <my-input
        v-model="station.city_fi"
        type="text"
        placeholder="city fi"
        required
      />
      <my-input
        v-model="station.city_sv"
        type="text"
        placeholder="city se"
        required
      />
      <my-input
        v-model="station.operator"
        type="text"
        placeholder="operator"
        required
      />
      <my-input
        v-model="station.capacity"
        type="number"
        placeholder="capacity"
        min="0"
      />
      <my-button class="btn-add" type="submit">Add station</my-button>
    </form>
    <div v-show="error">Invalid address</div>
  </div>
</template>

<script>
import opencage from "opencage-api-client";

export default {
  data() {
    return {
      station: {
        name_fi: "",
        name_sv: "",
        name_en: "",
        address_fi: "",
        address_sv: "",
        city_fi: "",
        city_sv: "",
        operator: "",
        capacity: 0,
        coordinate_x: null,
        coordinate_y: null,
      },
      error: false,
    };
  },
  methods: {
    async createStation() {
      await this.geocodeAddress();

      this.$emit("add", this.station);

      this.station = {
        name_fi: "",
        name_sv: "",
        name_en: "",
        address_fi: "",
        address_sv: "",
        city_fi: "",
        city_sv: "",
        operator: "",
        capacity: 0,
        coordinate_x: null,
        coordinate_y: null,
      };
    },
    async geocodeAddress() {
      const apiKey = "eaf2b19e73e14639b974825ea873abe2";
      const address = this.station.address_fi;

      try {
        const response = await opencage.geocode({ q: address, key: apiKey });

        if (response.status.code === 200) {
          const { lat, lng } = response.results[0].geometry;
          this.station.coordinate_x = lng;
          this.station.coordinate_y = lat;
        } else {
          console.error("Geocoding failed:", response.status.message);
        }
      } catch (error) {
        this.error = true;
        console.error("Error during geocoding:", error);
      }
    },
  },
};
</script>

<style scoped>
form {
  display: flex;
  flex-direction: column;
}

.btn-add {
  align-self: flex-end;
  margin-top: 15px;
}
</style>
