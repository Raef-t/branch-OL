import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  modals: {
    addExam: false,
    addPayment: false,
    addStudent: false,
    addTeacher: false,
    addEmployee: false,
  },
};

const uiSlice = createSlice({
  name: "ui",
  initialState,
  reducers: {
    setAddExamModal: (state, action) => {
      state.modals.addExam = action.payload;
    },
    setAddPaymentModal: (state, action) => {
      state.modals.addPayment = action.payload;
    },
    setAddStudentModal: (state, action) => {
      state.modals.addStudent = action.payload;
    },
    setAddTeacherModal: (state, action) => {
      state.modals.addTeacher = action.payload;
    },
    setAddEmployeeModal: (state, action) => {
      state.modals.addEmployee = action.payload;
    },
  },
});

export const {
  setAddExamModal,
  setAddPaymentModal,
  setAddStudentModal,
  setAddTeacherModal,
  setAddEmployeeModal,
} = uiSlice.actions;
export default uiSlice.reducer;
