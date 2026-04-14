"use client";

import { useEffect, useState } from "react";
import { Trash2, AlertTriangle, Info, CheckCircle, Loader2, XCircle } from "lucide-react";
import { useLazyGetDeletionReportQuery } from "@/store/services/studentsApi";

export default function StudentDeleteModal({
  isOpen,
  onClose,
  onConfirm,
  loading = false,
  student,
}) {
  const [fetchReport, { data: reportRes, isFetching: loadingReport, isError, error }] =
    useLazyGetDeletionReportQuery();
  const [isPermanent, setIsPermanent] = useState(false);

  const report = reportRes?.data || null;

  useEffect(() => {
    if (isOpen && student?.id) {
      fetchReport(student.id);
      setIsPermanent(false);
    }
  }, [isOpen, student, fetchReport]);

  if (!isOpen) return null;

  const hasRestrictions =
    report?.restrictions?.administrative?.length > 0 ||
    report?.restrictions?.educational?.length > 0;

  const hasEducational = report?.restrictions?.educational?.length > 0;
  const isLastStudent = report?.family_cleanup?.is_last_student;

  return (
    <div className="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div className="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        {/* HEADER */}
        <div className="bg-red-50 p-6 flex items-center gap-4 border-b border-red-100">
          <div className="bg-red-100 p-3 rounded-full text-red-600">
            <Trash2 className="w-6 h-6" />
          </div>
          <div className="text-right flex-1">
            <h2 className="text-xl font-bold text-red-900">حذف طالب</h2>
            <p className="text-red-700 text-sm">
              أنت على وشك حذف سجل: {student?.full_name || "..."}
            </p>
          </div>
        </div>

        {/* CONTENT */}
        <div className="p-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar text-right" dir="rtl">
          {loadingReport ? (
            <div className="flex flex-col items-center py-10 space-y-4">
              <Loader2 className="w-10 h-10 text-blue-600 animate-spin" />
              <p className="text-gray-500">جاري تحليل بيانات الطالب وارتباطاته...</p>
            </div>
          ) : isError ? (
            <div className="bg-red-50 border border-red-200 p-6 rounded-xl flex flex-col items-center gap-3 text-center">
              <XCircle className="w-10 h-10 text-red-600" />
              <div>
                <p className="text-red-900 font-bold">فشل تحليل البيانات</p>
                <p className="text-red-700 text-sm">{error?.data?.message || "حدث خطأ غير متوقع أثناء جلب تقرير الحذف."}</p>
              </div>
              <button 
                onClick={() => fetchReport(student.id)}
                className="mt-2 text-sm text-red-600 underline hover:text-red-800"
              >
                إعادة المحاولة
              </button>
            </div>
          ) : report ? (
            <>
              {/* STATUS CARD */}
              {!hasRestrictions ? (
                <div className="bg-green-50 border border-green-200 p-5 rounded-xl flex items-start gap-4">
                  <div className="bg-green-100 p-2 rounded-full shrink-0">
                    <CheckCircle className="w-6 h-6 text-green-600" />
                  </div>
                  <div>
                    <p className="text-green-900 font-bold text-base mb-1">جاهز للحذف الآمن</p>
                    <p className="text-green-800 text-sm leading-relaxed">
                      تم التحقق! هذا الطالب ليس لديه أي ارتباطات (عقود، امتحانات، أو حضور). يمكنك حذفه الآن بأمان وبشكل مباشر.
                    </p>
                  </div>
                </div>
              ) : (
                <div className="space-y-4">
                  <div className="bg-amber-50 border border-amber-200 p-4 rounded-xl flex items-start gap-3 shadow-sm">
                    <AlertTriangle className="w-6 h-6 text-amber-600 mt-1 shrink-0" />
                    <div>
                      <p className="text-amber-900 font-bold text-base mb-2 leading-tight">
                        قيود تمنع الحذف العادي:
                      </p>
                      
                      {/* ADMIN RESTRICTIONS */}
                      {report.restrictions.administrative.length > 0 && (
                        <div className="mb-4">
                          <span className="text-sm font-bold text-amber-700 block mb-2 underline decoration-amber-200 underline-offset-4">ارتباطات سيتم تنظيفها تلقائياً (إدارية):</span>
                          <div className="grid grid-cols-2 gap-2">
                            {report.restrictions.administrative.map((r, i) => (
                              <div key={i} className="flex items-center gap-2 text-xs text-amber-800 bg-white/50 p-1.5 rounded-md border border-amber-100">
                                <span className="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                                {r}
                              </div>
                            ))}
                          </div>
                        </div>
                      )}

                      {/* EDUCATIONAL RESTRICTIONS */}
                      {report.restrictions.educational.length > 0 && (
                        <div>
                          <span className="text-sm font-bold text-red-700 block mb-2 underline decoration-red-200 underline-offset-4">تحذير: ارتباطات تعليمية حساسة:</span>
                          <div className="grid grid-cols-2 gap-2">
                            {report.restrictions.educational.map((r, i) => (
                              <div key={i} className="flex items-center gap-2 text-xs text-red-800 bg-red-50 p-1.5 rounded-md border border-red-100">
                                <span className="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                {r}
                              </div>
                            ))}
                          </div>
                        </div>
                      )}
                    </div>
                  </div>

                  {/* FAMILY CLEANUP WARNING */}
                  {isLastStudent && (
                    <div className="bg-red-50 border border-red-200 p-4 rounded-xl flex items-start gap-3">
                      <div className="bg-red-100 p-1.5 rounded-md">
                        <Info className="w-5 h-5 text-red-700 shrink-0" />
                      </div>
                      <p className="text-red-900 text-sm leading-relaxed">
                        <span className="font-bold block text-base mb-1">تنبيه تنظيف العائلة:</span> 
                        هذا هو الطالب الأخير. حذف الطالب "نهائياً" سيؤدي إلى **حذف سجلات الأب والأم والعائلة** بالكامل من النظام.
                      </p>
                    </div>
                  )}

                  {/* PERMANENT TOGGLE */}
                  <div className="pt-2">
                     <label className="flex items-center gap-4 p-5 bg-white border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-red-200 hover:bg-red-50/30 transition-all shadow-sm has-[:checked]:border-red-500 has-[:checked]:bg-red-50/50 group">
                        <div className="relative flex items-center justify-center">
                            <input
                              type="checkbox"
                              className="peer w-6 h-6 text-red-600 rounded-lg border-gray-300 focus:ring-red-500 transition-all cursor-pointer"
                              checked={isPermanent}
                              onChange={(e) => setIsPermanent(e.target.checked)}
                            />
                        </div>
                        <div className="flex-1">
                          <span className="block text-base font-bold text-gray-900 group-hover:text-red-900 transition-colors">تفعيل الحذف النهائي والشامل</span>
                          <span className="block text-sm text-gray-500 leading-normal mt-1">
                             أوافق على قيام النظام بحذف الطالب وكافة الارتباطات المذكورة أعلاه بشكل آلي ونهائي.
                          </span>
                        </div>
                      </label>
                  </div>
                </div>
              )}
            </>
          ) : null}
        </div>

        {/* FOOTER */}
        <div className="bg-gray-50 p-6 flex gap-4 border-t border-gray-100">
          <button
            onClick={onClose}
            disabled={loading}
            className="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-100 disabled:opacity-50 transition-all active:scale-95"
          >
            إلغاء
          </button>
          <button
            onClick={() => onConfirm(isPermanent)}
            disabled={loading || loadingReport || isError || (hasRestrictions && !isPermanent)}
            className={`
              flex-[1.5] px-8 py-3 rounded-xl font-bold flex items-center justify-center gap-2 transition-all active:scale-95
              ${
                isPermanent
                  ? "bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-200"
                  : hasRestrictions
                  ? "bg-gray-300 text-gray-500 cursor-not-allowed"
                  : "bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-200"
              }
            `}
          >
            {loading ? (
              <Loader2 className="w-5 h-5 animate-spin" />
            ) : (
              <Trash2 className="w-5 h-5" />
            )}
            {isPermanent ? "تأكيد الحذف الشامل" : "حذف السجل"}
          </button>
        </div>
      </div>
    </div>
  );
}
