// src/services/http.js
import axios from "axios";

// Lấy base URL từ .env
// const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || "/api";

const http = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    "Content-Type": "application/json",
  },
});

// Deploy lên server thì dùng URL tuyệt đối, còn khi chạy local thì dùng URL tương đối để tránh lỗi CORS
// const http = axios.create({
//   baseURL: "https://api.thanh360.site/api",
//   headers: {
//     "Content-Type": "application/json",
//   },
// });

// Thêm token vào mỗi request
http.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default http;