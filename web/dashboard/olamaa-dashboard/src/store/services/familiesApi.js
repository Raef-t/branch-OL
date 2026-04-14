// src/store/services/familiesApi.js
import { createApi } from "@reduxjs/toolkit/query/react";
import ENDPOINTS from "@/lib/constants/endpoints";
import { baseApiConfig } from "./baseApi";

export const familiesApi = createApi({
    reducerPath: "familiesApi",
    ...baseApiConfig,
    tagTypes: ["Families"],

    endpoints: (builder) => ({
        // GET /api/families
        getFamilies: builder.query({
            query: (params) => ({
                url: ENDPOINTS.FAMILIES,
                method: "GET",
                params,
            }),
            providesTags: (r) =>
                r?.data
                    ? [
                        ...(Array.isArray(r?.data) ? r.data : Array.isArray(r?.data?.data) ? r.data.data : []).map(({ id }) => ({ type: "Families", id })),
                        { type: "Families", id: "LIST" },
                    ]
                    : [{ type: "Families", id: "LIST" }],
        }),

        // GET /api/families/:id
        getFamily: builder.query({
            query: (id) => ({
                url: `${ENDPOINTS.FAMILIES}/${id}`,
                method: "GET",
            }),
            providesTags: (r, e, id) => [{ type: "Families", id }],
        }),

        // POST /api/families
        addFamily: builder.mutation({
            query: (data) => ({
                url: ENDPOINTS.FAMILIES,
                method: "POST",
                data,
            }),
            invalidatesTags: [{ type: "Families", id: "LIST" }],
        }),

        // PUT /api/families/:id
        updateFamily: builder.mutation({
            query: ({ id, ...data }) => ({
                url: `${ENDPOINTS.FAMILIES}/${id}`,
                method: "PUT",
                data,
            }),
            invalidatesTags: (r, e, arg) => [
                { type: "Families", id: arg?.id },
                { type: "Families", id: "LIST" },
            ],
        }),

        // DELETE /api/families/:id
        deleteFamily: builder.mutation({
            query: (arg) => {
                const id = typeof arg === "object" ? arg.id : arg;
                return {
                    url: `${ENDPOINTS.FAMILIES}/${id}`,
                    method: "DELETE",
                };
            },
            invalidatesTags: (r, e, arg) => {
                const id = typeof arg === "object" ? arg.id : arg;
                return [
                    { type: "Families", id },
                    { type: "Families", id: "LIST" },
                ];
            },
        }),

        // POST /api/families/:id/activate-user
        activateFamilyUser: builder.mutation({
            query: (familyId) => ({
                url: `${ENDPOINTS.FAMILIES}/${familyId}/activate-user`,
                method: "POST",
            }),
            invalidatesTags: (r, e, familyId) => [
                { type: "Families", id: familyId },
                { type: "Families", id: "LIST" },
            ],
        }),
        
        // POST /api/users/:id/reset-password
        resetPassword: builder.mutation({
            query: (userId) => ({
                url: `/users/${userId}/reset-password`,
                method: "POST",
            }),
            // Note: Since this is more of a user action, we might not need to invalidate family list, 
            // but it's good to keep it consistent if needed.
        }),
    }),
});

export const {
    useGetFamiliesQuery,
    useGetFamilyQuery,
    useLazyGetFamilyQuery,
    useAddFamilyMutation,
    useUpdateFamilyMutation,
    useDeleteFamilyMutation,
    useActivateFamilyUserMutation,
    useResetPasswordMutation,
} = familiesApi;
