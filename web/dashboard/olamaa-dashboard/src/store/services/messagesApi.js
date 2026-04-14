"use client";

import { createApi } from "@reduxjs/toolkit/query/react";
import axios from "axios";

const isolatedBaseQuery =
  () =>
  async ({ url, method = "GET", data, params, headers }) => {
    try {
      const getBase = () => {
        if (typeof window === "undefined") return "";
        const p = window.location.pathname;
        if (p.startsWith("/test/")) return "/test";
        return "";
      };

      const finalUrl = `${getBase()}${url.replace("/api/", "/next-api/")}`;

      const result = await axios({
        url: finalUrl,
        method,
        data,
        params,
        headers,
        timeout: 30000,
      });

      return { data: result.data };
    } catch (axiosError) {
      const err = axiosError;

      return {
        error: {
          status: err?.response?.status,
          data: err?.response?.data || err?.message || "Network Error",
        },
      };
    }
  };

export const messagesApi = createApi({
  reducerPath: "messagesApi",
  baseQuery: isolatedBaseQuery(),
  endpoints: (builder) => ({
    sendSingleSms: builder.mutation({
      query: ({ phone, message, lang = 0 }) => ({
        url: "/api/send-sms",
        method: "POST",
        data: {
          phone,
          message,
          lang,
        },
        headers: {
          "Content-Type": "application/json",
        },
      }),
    }),
  }),
});

export const { useSendSingleSmsMutation } = messagesApi;