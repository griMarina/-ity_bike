import { createRouter, createWebHashHistory } from "vue-router";
import HomeView from "@/views/HomeView.vue";
import StationsView from "@/views/StationsView.vue";
import StationView from "@/views/StationView.vue";
import TripsView from "@/views/TripsView.vue";
// import TripView from "@/views/TripView.vue";

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    {
      path: "/",
      component: HomeView,
    },
    {
      path: "/stations",
      component: StationsView,
    },
    {
      path: "/stations/:id",
      component: StationView,
    },
    {
      path: "/trips",
      component: TripsView,
    },
  ],
});

export default router;
