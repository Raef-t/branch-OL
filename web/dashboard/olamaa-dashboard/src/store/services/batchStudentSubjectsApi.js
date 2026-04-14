import { createApi } from "@reduxjs/toolkit/query/react";
import ENDPOINTS from "@/lib/constants/endpoints";
import { baseApiConfig } from "./baseApi";

export const batchStudentSubjectsApi = createApi({
  reducerPath: "batchStudentSubjectsApi",
  ...baseApiConfig,
  tagTypes: ["BatchStudentSubjects"],

  endpoints: (builder) => ({
    // ربط مواد لطالب مسجل في دفعة (طالب جزئي)
    assignStudentSubjects: builder.mutation({
      query: ({ batch_student_id, batch_subject_ids, status = "active" }) => ({
        url: ENDPOINTS.BATCH_STUDENT_SUBJECTS,
        method: "POST",
        data: { batch_student_id, batch_subject_ids, status },
      }),
      invalidatesTags: ["BatchStudentSubjects"],
    }),

    // تحديث حالة مادة لطالب
    updateStudentSubjectStatus: builder.mutation({
      query: ({ id, status }) => ({
        url: `${ENDPOINTS.BATCH_STUDENT_SUBJECTS}/${id}`,
        method: "PUT",
        data: { status },
      }),
      invalidatesTags: ["BatchStudentSubjects"],
    }),

    // حذف مادة من تسجيل الطالب
    removeStudentSubject: builder.mutation({
      query: (id) => ({
        url: `${ENDPOINTS.BATCH_STUDENT_SUBJECTS}/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["BatchStudentSubjects"],
    }),
    
    // جلب بيانات تسجيل الطالب (كامل أو جزئي)
    getBatchStudentDetails: builder.query({
        query: (id) => ({
            url: `${ENDPOINTS.BATCH_STUDENTS}/${id}`,
            method: "GET",
        }),
        providesTags: (r, e, id) => [{ type: "BatchStudentSubjects", id }],
    })
  }),
});

export const {
  useAssignStudentSubjectsMutation,
  useUpdateStudentSubjectStatusMutation,
  useRemoveStudentSubjectMutation,
  useGetBatchStudentDetailsQuery,
} = batchStudentSubjectsApi;
