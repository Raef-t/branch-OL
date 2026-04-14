"use client";

import { useMemo } from "react";
import { useSelector, useDispatch } from "react-redux";
import {
  setAddExamModal,
  setAddPaymentModal,
  setAddStudentModal,
  setAddTeacherModal,
  setAddEmployeeModal,
} from "@/store/slices/uiSlice";
import ExamAddModal from "@/app/(dashboard)/exams/components/ExamAddModal";
import PaymentAddModal from "@/app/(dashboard)/payments/components/PaymentAddModal";
import AddStudentModal from "@/app/(dashboard)/students/components/addStudent/AddStudentModal";
import AddTeacherModal from "@/app/(dashboard)/teachers/components/steps/AddTeacherModal";
import AddEmployeeModal from "@/app/(dashboard)/employees/components/AddEmployeeModal";
import { useAddExamMutation } from "@/store/services/examsApi";
import { useAddPaymentMutation } from "@/store/services/paymentsApi";
import { useGetStudentsDetailsQuery } from "@/store/services/studentsApi";
import { notify } from "@/lib/helpers/toastify";
import { useRouter } from "next/navigation";

export default function GlobalActionModals() {
  const dispatch = useDispatch();
  const router = useRouter();
  const { addExam, addPayment, addStudent, addTeacher, addEmployee } =
    useSelector((state) => state.ui.modals);

  const [addExamMutation, { isLoading: addingExam }] = useAddExamMutation();
  const [addPaymentMutation, { isLoading: addingPayment }] = useAddPaymentMutation();

  const { data: studentsRes } = useGetStudentsDetailsQuery();
  const students = useMemo(() => studentsRes?.data || studentsRes || [], [studentsRes]);

  const auth = typeof window !== "undefined" ? localStorage.getItem("auth") : null;
  const branchId = useMemo(() => {
    if (!auth) return "";
    try {
      const parsed = JSON.parse(auth);
      return String(parsed?.user?.institute_branch_id || "");
    } catch {
      return "";
    }
  }, [auth]);

  const handleAddExamSubmit = async (payload) => {
    try {
      await addExamMutation(payload).unwrap();
      notify.success("تمت إضافة المذاكرة بنجاح");
      dispatch(setAddExamModal(false));
    } catch (err) {
      notify.error(err?.data?.message || err?.message || "فشل إضافة المذاكرة");
    }
  };

  const handleAddPaymentSubmit = async (payload) => {
    try {
      const res = await addPaymentMutation(payload).unwrap();
      notify.success("تمت إضافة الدفعة بنجاح");
      dispatch(setAddPaymentModal(false));
      return res;
    } catch (err) {
      notify.error(err?.data?.message || err?.message || "فشل إضافة الدفعة");
      throw err;
    }
  };

  return (
    <>
      <ExamAddModal
        open={addExam}
        onClose={() => dispatch(setAddExamModal(false))}
        onSubmit={handleAddExamSubmit}
        loading={addingExam}
      />

      <PaymentAddModal
        open={addPayment}
        onClose={() => dispatch(setAddPaymentModal(false))}
        onSubmit={handleAddPaymentSubmit}
        students={students}
        defaultInstituteBranchId={branchId}
        loading={addingPayment}
      />

      <AddStudentModal
        isOpen={addStudent}
        onClose={() => dispatch(setAddStudentModal(false))}
        student={null}
        onAdded={() => {
          dispatch(setAddStudentModal(false));
          router.replace("/students");
        }}
        onAssignToBatch={(studentData) => {
          dispatch(setAddStudentModal(false));
          router.push(`/students?addBatch=${studentData?.id || ""}`);
        }}
      />

      <AddTeacherModal
        isOpen={addTeacher}
        onClose={() => dispatch(setAddTeacherModal(false))}
      />

      <AddEmployeeModal
        isOpen={addEmployee}
        onClose={() => dispatch(setAddEmployeeModal(false))}
        employee={null}
      />
    </>
  );
}
