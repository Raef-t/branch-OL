"use client";

import { Loader2 } from "lucide-react";

export default function StepButtonsSmart({
  step = 1, // الخطوة الحالية
  total = 1, // عدد كل الخطوات
  isEdit = false, // هل تعديل أم إضافة
  loading = false, // حالة التحميل
  onNext,
  onBack,
  onSkip, // ✅ خاصية التخطي الجديدة
  skipLabel = "تخطي هذه الخطوة",
  submitLabel,
  backDisabled = false,
  type = "button", // ✅ إضافة خاصية نوع الزر (button أو submit)
}) {
  const isLastStep = step === total;

  // تحديد نص الزر تلقائياً
  let nextLabel = "التالي";

  if (total === 1) {
    nextLabel = submitLabel ?? (isEdit ? "تعديل البيانات" : "حفظ");
  } else if (isLastStep) {
    nextLabel = "إنهاء";
  }

  return (
    <div className="flex justify-between items-center mt-8 w-full select-none">
      {/* زر السابق */}
      {total > 1 ? (
        <button
          type="button"
          onClick={onBack}
          disabled={step === 1 || backDisabled}
          className={`
            flex items-center gap-2 px-5 py-1 rounded-md border text-sm transition
            ${step === 1 || backDisabled
              ? "cursor-not-allowed text-gray-400 border-gray-200 bg-gray-50"
              : "text-[#6F013F] border-[#F3C3D9] bg-[#FDF2F8] hover:bg-[#F9E1EE]"
            }
          `}
        >
          « السابق
        </button>
      ) : (
        <div />
      )}

      {/* مجموعة (التالي + تخطي) ليكونوا بجانب بعض وبنفس الحجم */}
      <div className="flex items-center gap-2">
        {onSkip && (
          <button
            type="button"
            onClick={onSkip}
            disabled={loading}
            className="
              flex items-center gap-2 px-5 py-1 rounded-md border text-sm transition
              text-gray-500 border-gray-300 bg-white hover:bg-gray-50
              disabled:cursor-not-allowed disabled:opacity-50
            "
          >
            {skipLabel}
          </button>
        )}

        <button
          type={type}
          onClick={onNext}
          disabled={loading}
          className="
            flex items-center gap-2 px-5 py-1 rounded-md border text-sm transition
            text-[#6F013F] border-[#F3C3D9] bg-white hover:bg-[#FDF2F8]
            disabled:cursor-not-allowed disabled:text-gray-400 disabled:border-gray-200 disabled:bg-gray-50
          "
        >
          {loading ? <Loader2 className="w-4 h-4 animate-spin" /> : null}
          {nextLabel} »
        </button>
      </div>
    </div>
  );
}
