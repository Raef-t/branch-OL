import { createApi } from "@reduxjs/toolkit/query/react";
import ENDPOINTS from "@/lib/constants/endpoints";
import { baseApiConfig } from "./baseApi";

export const notificationsApi = createApi({
  reducerPath: "notificationsApi",
  ...baseApiConfig,
  tagTypes: ["Notifications"],

  endpoints: (builder) => ({
    createNotification: builder.mutation({
      query: (data) => ({
        url: ENDPOINTS.NOTIFICATIONS,
        method: "POST",
        data,
      }),
      invalidatesTags: ["Notifications"],
    }),

    getNotifications: builder.query({
      query: (params) => ({
        url: ENDPOINTS.NOTIFICATIONS,
        method: "GET",
        params,
      }),
      providesTags: ["Notifications"],
    }),
  }),
});

export const {
  useCreateNotificationMutation,
  useGetNotificationsQuery,
} = notificationsApi;
