"use client";

import { useEffect } from "react";
import { X } from "lucide-react";
import { notify } from "@/lib/helpers/toastify";
import { useForm, Controller } from "react-hook-form";

import InputField from "@/components/common/InputField";
import SearchableSelect from "@/components/common/SearchableSelect";
import StepButtonsSmart from "@/components/common/StepButtonsSmart";
import ModalLoader from "@/components/common/ModalLoader";

import {
  useGetRecordQuery,
  useUpdateRecordMutation,
  useAddRecordMutation,
} from "@/store/services/academicRecordsApi";

const RECORD_TYPES = [
  { key: "ninth_grade", value: "ninth_grade", label: "ناجح تاسع" },
  { key: "bac_passed", value: "bac_passed", label: "ناجح بكالوريا" },
  { key: "bac_failed", value: "bac_failed", label: "راسب بكالوريا" },
];

export default function EditAcademicRecordModal({
  open,
  onClose,
  recordId,
  studentId,
  onSaved,
}) {
  const isEdit = !!recordId;

  const { data, isFetching } = useGetRecordQuery(recordId, {
    skip: !open || !isEdit,
  });

  const record = data?.data;

  const [updateRecord, { isLoading: isUpdating }] = useUpdateRecordMutation();
  const [addRecord, { isLoading: isAdding }] = useAddRecordMutation();

  const isLoading = isUpdating || isAdding;

  const form = useForm({ mode: "onTouched" });
  const { control, register, reset, getValues, formState } = form;
  const { errors } = formState;

  useEffect(() => {
    if (!open) return;

    if (isEdit && record) {
      reset({
        record_type: record.record_type ?? "",
        total_score: record.total_score ? Number(record.total_score) : "",
        year: record.year ?? "",
        description: record.description ?? "",
      });
    } else if (!isEdit) {
      reset({
        record_type: "",
        total_score: "",
        year: "",
        description: "",
      });
    }
  }, [record, reset, open, isEdit]);

  if (!open) return null;

  const handleSave = async () => {
    try {
      const v = getValues();
      const payload = {
        record_type: v.record_type || null,
        total_score:
          v.total_score === "" || Number.isNaN(v.total_score)
            ? null
            : v.total_score,
        year: v.year === "" || Number.isNaN(v.year) ? null : v.year,
        description: v.description || null,
      };

      if (isEdit) {
        await updateRecord({
          id: recordId,
          ...payload,
        }).unwrap();
        notify.success("تم تعديل السجل الأكاديمي");
      } else {
        await addRecord({
          student_id: studentId,
          ...payload,
        }).unwrap();
        notify.success("تم إضافة السجل الأكاديمي");
      }

      onSaved?.();
      onClose?.();
    } catch (e) {
      notify.error(isEdit ? "فشل تعديل السجل الأكاديمي" : "فشل إضافة السجل الأكاديمي");
      console.error(e);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/40 z-50 flex justify-start">
      <div className="w-[520px] bg-white h-full p-6 overflow-y-auto">
        <div className="flex justify-between mb-4">
          <h2 className="text-[#6F013F] font-semibold">
            {isEdit ? "تعديل السجل الأكاديمي" : "إضافة سجل أكاديمي"}
          </h2>
          <button onClick={onClose}>
            <X />
          </button>
        </div>

        {isFetching && isEdit ? (
          <ModalLoader message="جاري تحميل السجل الأكاديمي..." />
        ) : (
          <div className="space-y-4">
            <Controller
              control={control}
              name="record_type"
              render={({ field }) => (
                <SearchableSelect
                  label="نوع السجل الأكاديمي"
                  value={field.value || ""}
                  onChange={field.onChange}
                  options={RECORD_TYPES}
                  placeholder="اختر نوع السجل"
                  allowClear
                />
              )}
            />
            <p className="text-xs text-red-500">
              {errors.record_type?.message}
            </p>

            <InputField
              label="المجموع"
              type="number"
              register={register("total_score", {
                valueAsNumber: true,
              })}
              error={errors.total_score?.message}
            />

            <InputField
              label="السنة"
              type="number"
              register={register("year", {
                valueAsNumber: true,
                min: { value: 1900, message: "السنة غير صحيحة" },
                max: {
                  value: new Date().getFullYear() + 1,
                  message: "السنة غير صحيحة",
                },
                onChange: (e) => {
                  e.target.value = String(e.target.value)
                    .replace(/\D/g, "")
                    .slice(0, 4);
                },
              })}
              error={errors.year?.message}
            />

            <textarea
              rows={3}
              {...register("description")}
              placeholder="الوصف (اختياري)"
              className="rounded-xl p-2 text-sm w-full border border-gray-200 outline-none"
            />

            <StepButtonsSmart
              step={1}
              total={1}
              onNext={handleSave}
              loading={isLoading}
              submitLabel="حفظ"
            />
          </div>
        )}
      </div>
    </div>
  );
}
