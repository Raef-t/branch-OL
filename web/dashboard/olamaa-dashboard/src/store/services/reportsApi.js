import { createApi } from "@reduxjs/toolkit/query/react";
import axios from "@/lib/config/axiosConfig";

/* ================= baseQuery ================= */
const axiosBaseQuery =
  ({ baseUrl } = { baseUrl: "" }) =>
    async ({ url, method, data, params, responseType, headers }) => {
      try {
        const result = await axios({
          url: baseUrl + url,
          method,
          data,
          params,
          responseType,
          headers,
        });

        return { data: result.data };
      } catch (axiosError) {
        const err = axiosError;
        return {
          error: {
            status: err.response?.status,
            data: err.response?.data || err.message,
          },
        };
      }
    };

export const reportsApi = createApi({
  reducerPath: "reportsApi",
  baseQuery: axiosBaseQuery({ baseUrl: "" }),
  tagTypes: ["AttendanceReport"],
  endpoints: (builder) => ({
    // Fetch students filtered by branch and batches
    getStudentsForReport: builder.query({
      query: ({ institute_branch_id, batch_ids }) => ({
        url: "/students/reports/attendance/students",
        method: "GET",
        params: {
          institute_branch_id,
          "batch_ids[]": batch_ids, // Format as array for Laravel
        },
      }),
    }),

    // Generate the full attendance report
    generateAttendanceReport: builder.query({
      query: (params) => ({
        url: "/students/reports/attendance/generate",
        method: "GET",
        params: {
          ...params,
          "batch_ids[]": params.batch_ids, // Ensure array format
        },
      }),
      providesTags: ["AttendanceReport"],
    }),

    // Generate the student data report
    generateStudentDataReport: builder.query({
      query: (params) => ({
        url: "/students/reports/data/generate",
        method: "GET",
        params: {
          ...params,
          "batch_ids[]": params.batch_ids, // Ensure array format
        },
      }),
    }),

    // Generate the exams report
    generateExamsReport: builder.query({
      query: (params) => ({
        url: "/students/reports/exams/generate",
        method: "GET",
        params: {
          ...params,
          "batch_ids[]": params.batch_ids, // Ensure array format
        },
      }),
    }),

    // Generate the bus report
    generateBusReport: builder.query({
      query: (params) => ({
        url: "/students/reports/buses/generate",
        method: "GET",
        params: {
          ...params,
          "bus_ids[]": params.bus_ids, // Ensure array format
          "batch_ids[]": params.batch_ids, // Ensure array format
        },
      }),
    }),

    // Generate the phone report
    generatePhoneReport: builder.query({
      query: (params) => ({
        url: "/students/reports/phones/generate",
        method: "GET",
        params: {
          ...params,
          "batch_ids[]": params.batch_ids, // Ensure array format
        },
      }),
    }),
  }),
});

export const {
  useGetStudentsForReportQuery,
  useGenerateAttendanceReportQuery,
  useLazyGenerateAttendanceReportQuery,
  useLazyGenerateStudentDataReportQuery,
  useLazyGenerateExamsReportQuery,
  useLazyGenerateBusReportQuery,
  useLazyGeneratePhoneReportQuery,
} = reportsApi;
