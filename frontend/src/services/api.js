import axios from "axios";

// Set the base URL for all requests made with this Axios instance to "http://localhost:8080/"
const api = axios.create({
  baseURL: "http://localhost:8080/",
});

export default api;
