"use client";

import { useEffect, useMemo, useState } from "react";
import { X, Save, CheckSquare, Square } from "lucide-react";
import { notify } from "@/lib/helpers/toastify";

import FormInput from "@/components/common/InputField";
import SearchableSelect from "@/components/common/SearchableSelect";

import { useGetSubjectsQuery } from "@/store/services/subjectsApi";
import { useGetTeachersBySubjectQuery } from "@/store/services/subjectsTeachersApi";
import {
  useAssignInstructorSubjectToBatchMutation,
  useUpdateBatchSubjectMutation,
} from "@/store/services/batcheSubjectsApi";

function normalizeArray(res) {
  if (Array.isArray(res)) return res;
  if (Array.isArray(res?.data)) return res.data;
  return [];
}

export default function AddBatchSubjectModal({
  isOpen,
  onClose,
  batchId,
  batchName,
  editingSubject = null,
}) {
  const [form, setForm] = useState({
    subject_id: "",
    instructor_subject_id: "",
    weekly_lessons: "1",
    allow_same_subject_same_day: false,
    max_lessons_per_day: "1",
    notes: "",
  });

  const { data: subjectsRes, isLoading: loadingSubjects } = useGetSubjectsQuery(
    undefined,
    { skip: !isOpen }
  );
  
  const subjects = useMemo(() => normalizeArray(subjectsRes), [subjectsRes]);

  const { data: teachersRes, isLoading: loadingTeachers } = useGetTeachersBySubjectQuery(
    form.subject_id,
    { skip: !isOpen || !form.subject_id }
  );

  const teachers = useMemo(() => normalizeArray(teachersRes), [teachersRes]);

  const [assignSubject, { isLoading: isAssigning }] = useAssignInstructorSubjectToBatchMutation();
  const [updateSubject, { isLoading: isUpdating }] = useUpdateBatchSubjectMutation();

  useEffect(() => {
    if (isOpen) {
      if (editingSubject) {
        setForm({
          subject_id: editingSubject.subject_id || "",
          instructor_subject_id: editingSubject.instructor_subject_id || "",
          weekly_lessons: String(editingSubject.weekly_lessons || "1"),
          allow_same_subject_same_day: !!editingSubject.allow_same_subject_same_day,
          max_lessons_per_day: String(editingSubject.max_lessons_per_day || "1"),
          notes: editingSubject.notes || "",
        });
      } else {
        setForm({
          subject_id: "",
          instructor_subject_id: "",
          weekly_lessons: "1",
          allow_same_subject_same_day: false,
          max_lessons_per_day: "1",
          notes: "",
        });
      }
    }
  }, [isOpen, editingSubject]);

  const subjectOptions = useMemo(() => {
    return subjects.map((s) => ({
      value: String(s.id),
      label: s.name,
    }));
  }, [subjects]);

  const teacherOptions = useMemo(() => {
    return teachers.map((t) => ({
      value: String(t.id), // This is the InstructorSubject ID
      label: t.instructor?.full_name || t.instructor?.name || "مدرس غير معروف",
    }));
  }, [teachers]);

  const handleSubmit = async (e) => {
    e.preventDefault();

    const payload = {
      batch_id: batchId,
      subject_id: form.subject_id ? Number(form.subject_id) : null,
      instructor_subject_id: form.instructor_subject_id ? Number(form.instructor_subject_id) : null,
      weekly_lessons: Number(form.weekly_lessons),
      allow_same_subject_same_day: form.allow_same_subject_same_day,
      max_lessons_per_day: form.max_lessons_per_day ? Number(form.max_lessons_per_day) : null,
      notes: form.notes,
    };

    try {
      if (editingSubject) {
        await updateSubject({ id: editingSubject.id, ...payload }).unwrap();
        notify.success("تم تحديث المادة بنجاح");
      } else {
        await assignSubject(payload).unwrap();
        notify.success("تم إضافة المادة للدورة بنجاح");
      }
      onClose();
    } catch (err) {
      notify.error(err?.data?.message || "حدث خطأ أثناء الحفظ");
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
      <div
        dir="rtl"
        className="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200"
      >
        {/* Header */}
        <div className="bg-[#8A1654] p-6 text-white flex items-center justify-between">
          <div>
            <h2 className="text-xl font-bold">
              {editingSubject ? "تعديل مادة" : "إضافة مادة للدورة"}
            </h2>
            <p className="text-pink-100 text-xs mt-1">الدورة: {batchName}</p>
          </div>
          <button
            onClick={onClose}
            className="p-2 hover:bg-white/20 rounded-full transition"
          >
            <X size={20} />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="p-6 space-y-5">
          <div className="bg-gray-50 border border-gray-100 rounded-2xl p-4 mb-2">
            <div className="flex justify-between items-center mb-2">
              <span className="text-xs text-gray-500">المادة</span>
              <span className="text-sm font-bold text-gray-800">{editingSubject?.subject_name || "—"}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-xs text-gray-500">المدرس</span>
              <span className="text-sm font-medium text-[#8A1654]">
                {editingSubject?.instructor_name && editingSubject.instructor_name !== "غير محدد" 
                  ? editingSubject.instructor_name 
                  : "لم يتم التعيين بعد"}
              </span>
            </div>
          </div>

          <div className="bg-blue-50/50 border border-blue-100 rounded-2xl p-4 space-y-3">
            <p className="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1">إعدادات الجدول الذكي</p>
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-1">
                <FormInput
                  label="الحصص الأسبوعية"
                  required
                  type="number"
                  min="1"
                  value={form.weekly_lessons}
                  onChange={(e) => setForm((p) => ({ ...p, weekly_lessons: e.target.value }))}
                />
                <p className="text-[10px] text-gray-500 pr-1">إجمالي الحصص في الأسبوع</p>
              </div>

              <div className="space-y-1">
                <FormInput
                  label="أقصى حصص يومياً"
                  type="number"
                  min="1"
                  value={form.max_lessons_per_day}
                  onChange={(e) => setForm((p) => ({ ...p, max_lessons_per_day: e.target.value }))}
                  placeholder="مثال: 2"
                />
                <p className="text-[10px] text-gray-500 pr-1">لمنع تراكم المادة في يوم واحد</p>
              </div>
            </div>

            <div 
              className="flex items-center gap-3 p-3 bg-white/80 rounded-xl cursor-pointer hover:bg-white transition border border-blue-50 shadow-sm"
              onClick={() => setForm(p => ({ ...p, allow_same_subject_same_day: !p.allow_same_subject_same_day }))}
            >
              {form.allow_same_subject_same_day ? (
                <CheckSquare className="text-[#8A1654]" size={20} />
              ) : (
                <Square className="text-gray-400" size={20} />
              )}
              <div className="flex flex-col">
                <span className="text-sm font-medium text-gray-700">
                  تكرار المادة في نفس اليوم
                </span>
                <span className="text-[10px] text-gray-500">للسماح بحصص منفصلة للمادة في نفس اليوم</span>
              </div>
            </div>
          </div>

          <FormInput
            label="ملاحظات"
            textarea
            rows={2}
            value={form.notes}
            onChange={(e) => setForm((p) => ({ ...p, notes: e.target.value }))}
            placeholder="أضف أي ملاحظات هنا..."
          />

          {/* Buttons */}
          <div className="flex gap-3 pt-2">
            <button
              type="submit"
              disabled={isAssigning || isUpdating}
              className="flex-1 h-12 bg-[#8A1654] text-white rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-[#701244] transition shadow-lg shadow-pink-100 disabled:opacity-50"
            >
              <Save size={18} />
              <span>{editingSubject ? "حفظ التعديلات" : "إضافة المادة"}</span>
            </button>
            <button
              type="button"
              onClick={onClose}
              className="flex-1 h-12 bg-gray-100 text-gray-700 rounded-2xl font-bold hover:bg-gray-200 transition"
            >
              إلغاء
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
