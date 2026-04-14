"use client";

import { useState, useMemo, useEffect } from "react";
import BaseModal from "@/components/common/BaseModal";
import SearchableSelect from "@/components/common/SearchableSelect";
import { MoveLeft, Loader2 } from "lucide-react";
import { notify } from "@/lib/helpers/toastify";

// APIs
import {
  useGetBatchStudentsQuery,
  useUpdateBatchStudentMutation,
} from "@/store/services/batchStudentsApi";

export default function TransferStudentModal({
  isOpen,
  onClose,
  initialBatchId = "", // ID of the old batch
  batches = [], // array of { value: id, label: name }
}) {
  const [selectedOldBatchId, setSelectedOldBatchId] = useState("");
  const [selectedStudentBatchId, setSelectedStudentBatchId] = useState(""); // This is the ID of the BatchStudent record
  const [selectedNewBatchId, setSelectedNewBatchId] = useState("");

  const [updateBatchStudent, { isLoading: isUpdating }] = useUpdateBatchStudentMutation();

  // Load students of the old batch
  const { data: studentsRes, isLoading: isLoadingStudents } = useGetBatchStudentsQuery(
    selectedOldBatchId,
    { skip: !selectedOldBatchId || !isOpen }
  );

  const students = useMemo(() => {
    return (studentsRes?.data || []).map((bs) => ({
      value: bs.id, // batch_student.id
      label: bs.full_name || `${bs.first_name} ${bs.last_name}`,
    }));
  }, [studentsRes]);

  useEffect(() => {
    if (isOpen) {
      setSelectedOldBatchId(initialBatchId);
      setSelectedStudentBatchId("");
      setSelectedNewBatchId("");
    }
  }, [isOpen, initialBatchId]);

  const handleTransfer = async () => {
    if (!selectedStudentBatchId || !selectedNewBatchId) return;

    try {
      await updateBatchStudent({
        id: selectedStudentBatchId,
        batch_id: Number(selectedNewBatchId),
      }).unwrap();

      notify.success("تم نقل الطالب بنجاح");
      onClose();
    } catch (err) {
      notify.error(err?.data?.message || "حدث خطأ أثناء نقل الطالب");
    }
  };

  return (
    <BaseModal
      open={isOpen}
      onClose={onClose}
      title={
        <div className="flex items-center justify-between w-full">
          <div />
          <div className="p-1.5 bg-[#FCE7F3] rounded-lg text-[#6F013F] shrink-0">
            <MoveLeft size={18} />
          </div>
          <span className="text-[#6F013F] font-bold text-lg pr-2">
            نقل طالب من شعبة لشعبة
          </span>
        </div>
      }
      widthClass="max-w-md"
      footer={
        <div className="flex flex-col gap-4 w-full pt-1">
          <div className="h-[1px] bg-gray-200 -mx-5 px-5 mb-2" />

          <div className="flex items-center gap-3 w-full justify-end pb-2">
            <button
              onClick={onClose}
              className="px-4 py-1.5 rounded-md bg-[#FCE7F3] text-[#6F013F] text-sm font-bold transition hover:opacity-90 grow sm:grow-0 min-w-[100px]"
            >
              إلغاء
            </button>
            <button
              onClick={handleTransfer}
              disabled={!selectedStudentBatchId || !selectedNewBatchId || isUpdating}
              className="px-4 py-1.5 rounded-md bg-[#6F013F] text-white text-sm font-bold transition hover:opacity-90 disabled:bg-gray-200 disabled:text-gray-400 shadow-sm grow sm:grow-0 min-w-[120px] flex items-center justify-center gap-2"
            >
              {isUpdating && <Loader2 size={16} className="animate-spin" />}
              تأكيد النقل
            </button>
          </div>
        </div>
      }
    >
      <div className="space-y-6 py-4">
        <SearchableSelect
          label="الشعبة القديمة"
          value={selectedOldBatchId}
          onChange={setSelectedOldBatchId}
          options={batches}
          placeholder="اختر الشعبة القديمة"
          disabled={!!initialBatchId}
        />

        <SearchableSelect
          label="اسم الطالب"
          value={selectedStudentBatchId}
          onChange={setSelectedStudentBatchId}
          options={students}
          placeholder={isLoadingStudents ? "جاري تحميل الطلاب..." : "اختر الطالب المراد نقله"}
          disabled={!selectedOldBatchId || isLoadingStudents}
        />

        <SearchableSelect
          label="الشعبة الجديدة"
          value={selectedNewBatchId}
          onChange={setSelectedNewBatchId}
          options={batches.filter((b) => String(b.value) !== String(selectedOldBatchId))}
          placeholder="اختر الشعبة الجديدة"
        />
      </div>
    </BaseModal>
  );
}
