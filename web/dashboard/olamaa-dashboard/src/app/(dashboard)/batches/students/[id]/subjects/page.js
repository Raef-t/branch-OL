"use client";
import { useEffect, useState, useMemo } from "react";
import { useParams, useRouter } from "next/navigation";
import { ArrowRight, Save, LayoutGrid, ListChecks, Info } from "lucide-react";
import { toast } from "react-hot-toast";

import Breadcrumb from "@/components/common/Breadcrumb";
import PageSkeleton from "@/components/common/PageSkeleton";
import DataTable from "@/components/common/DataTable";

import { 
  useGetBatchStudentDetailsQuery,
  useAssignStudentSubjectsMutation 
} from "@/store/services/batchStudentSubjectsApi";
import { useUpdateBatchStudentMutation } from "@/store/services/batchStudentsApi";

export default function PartialEnrollmentPage() {
  const { id } = useParams();
  const router = useRouter();

  const { data: detailsRes, isLoading, refetch } = useGetBatchStudentDetailsQuery(id);
  const [updateBatchStudent, { isLoading: isUpdatingStatus }] = useUpdateBatchStudentMutation();
  const [assignSubjects, { isLoading: isAssigningSubjects }] = useAssignStudentSubjectsMutation();

  const batchStudent = detailsRes?.data || detailsRes;
  const studentName = batchStudent?.student?.full_name || "—";
  const batchName = batchStudent?.batch?.name || "—";
  const allBatchSubjects = batchStudent?.batch?.batch_subjects || [];
  const currentlyEnrolledSubjects = batchStudent?.subjects || []; // From backend show()

  // Local state for management
  const [isPartial, setIsPartial] = useState(false);
  const [selectedSubjectIds, setSelectedSubjectIds] = useState([]);

  useEffect(() => {
    if (batchStudent) {
      setIsPartial(batchStudent.enrollment_type === "partial" || !!batchStudent.is_partial);
      // Map currently enrolled subjects to IDs
      const enrolledIds = currentlyEnrolledSubjects.map(s => s.batch_subject_id);
      setSelectedSubjectIds(enrolledIds);
    }
  }, [batchStudent]);

  const handleSave = async () => {
    try {
      // 1. Update the is_partial status on the BatchStudent record
      await updateBatchStudent({
        id: id,
        is_partial: isPartial
      }).unwrap();

      // 2. If partial, sync the subjects
      if (isPartial) {
        if (selectedSubjectIds.length === 0) {
          toast.error("يجب اختيار مادة واحدة على الأقل عند التفعيل الجزئي");
          return;
        }
        await assignSubjects({
          batch_student_id: id,
          batch_subject_ids: selectedSubjectIds,
          status: "active"
        }).unwrap();
      }

      toast.success("تم حفظ التغييرات بنجاح");
      refetch();
    } catch (error) {
      console.error(error);
      toast.error("حدث خطأ أثناء حفظ التغييرات");
    }
  };

  const columns = [
    { header: "المادة", key: "subject_name" },
    { 
        header: "الحالة", 
        key: "status_badge",
        render: (row) => {
            if (!row || !row.id) return null;
            return selectedSubjectIds.includes(row.id) ? (
                <span className="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100">
                    مدرجة
                </span>
            ) : (
                <span className="px-2 py-0.5 rounded-full bg-gray-50 text-gray-400 text-[10px] font-bold border border-gray-100">
                    غير مدرجة
                </span>
            )
        }
    },
  ];

  const tableData = useMemo(() => {
    if (!allBatchSubjects) return [];
    return allBatchSubjects
      .filter(bs => bs && bs.id)
      .map(bs => ({
        id: bs.id,
        subject_name: bs.subject?.name || "—",
      }));
  }, [allBatchSubjects]);

  if (isLoading) return <PageSkeleton />;

  return (
    <div dir="rtl" className="w-full min-h-screen p-4 md:p-6 bg-[#fcfcfd] flex flex-col gap-6">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="flex flex-col gap-1">
          <div className="flex items-center gap-2 mb-1">
             <button 
                onClick={() => router.back()}
                className="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 transition"
             >
                <ArrowRight size={20} />
             </button>
             <h1 className="text-xl font-bold text-gray-800">إدارة تخصيص المواد (التسجيل الجزئي)</h1>
          </div>
          <Breadcrumb />
        </div>

        <button
          onClick={handleSave}
          disabled={isUpdatingStatus || isAssigningSubjects}
          className="h-11 px-6 rounded-xl bg-[#6F013F] text-white text-sm font-bold shadow-lg shadow-[#6F013F]/20 hover:bg-[#5a0134] transition flex items-center gap-2 disabled:opacity-50"
        >
          <Save size={18} />
          <span>حفظ التغييرات</span>
        </button>
      </div>

      {/* Student Info Card */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-1 space-y-6">
            <div className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 className="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">بيانات الطالب والشعبة</h3>
                <div className="space-y-4">
                    <div>
                        <span className="text-[10px] text-gray-400 block mb-1">اسم الطالب</span>
                        <span className="text-sm font-bold text-gray-800">{studentName}</span>
                    </div>
                    <div>
                        <span className="text-[10px] text-gray-400 block mb-1">الشعبة المسجل بها</span>
                        <span className="text-sm font-bold text-[#B00069]">{batchName}</span>
                    </div>
                </div>
            </div>

            <div className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 className="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">نوع التسجيل</h3>
                <div className="flex flex-col gap-4">
                    <button
                        onClick={() => setIsPartial(false)}
                        className={`flex items-center gap-3 p-4 rounded-xl border-2 transition ${!isPartial ? 'border-[#6F013F] bg-[#6F013f]/5' : 'border-gray-50 bg-gray-50'}`}
                    >
                        <div className={`p-2 rounded-lg ${!isPartial ? 'bg-[#6F013F] text-white' : 'bg-white text-gray-400'}`}>
                            <LayoutGrid size={20} />
                        </div>
                        <div className="text-right">
                            <span className={`block text-sm font-bold ${!isPartial ? 'text-[#6F013F]' : 'text-gray-500'}`}>تسجيل كلي (Full)</span>
                            <span className="text-[10px] text-gray-400 italic">يتم تسجيل الطالب في كافة مواد الشعبة تلقائياً</span>
                        </div>
                    </button>

                    <button
                        onClick={() => setIsPartial(true)}
                        className={`flex items-center gap-3 p-4 rounded-xl border-2 transition ${isPartial ? 'border-amber-500 bg-amber-50/50' : 'border-gray-50 bg-gray-50'}`}
                    >
                        <div className={`p-2 rounded-lg ${isPartial ? 'bg-amber-500 text-white' : 'bg-white text-gray-400'}`}>
                            <ListChecks size={20} />
                        </div>
                        <div className="text-right">
                            <span className={`block text-sm font-bold ${isPartial ? 'text-amber-600' : 'text-gray-500'}`}>تسجيل جزئي (Partial)</span>
                            <span className="text-[10px] text-gray-400 italic">يمكنك اختيار مواد محددة فقط للطالب</span>
                        </div>
                    </button>
                </div>
            </div>

            <div className="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-3 text-blue-600">
                <Info size={20} className="shrink-0 mt-0.5" />
                <p className="text-[11px] leading-relaxed">
                    <b>ملاحظة:</b> في حالة "التسجيل الكلي"، سيعتبر النظام الطالب حاضراً في جميع المواد. في حالة "التسجيل الجزئي"، سيظهر الطالب فقط في قوائم التحضير والامتحانات للمواد المختارة.
                </p>
            </div>
        </div>

        {/* Subjects Selection */}
        <div className="lg:col-span-2">
            <div className={`bg-white rounded-2xl shadow-sm border border-gray-100 transition-opacity ${!isPartial ? 'opacity-40 pointer-events-none' : 'opacity-100'}`}>
                <div className="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 className="text-sm font-bold text-gray-800">قائمة مواد الشعبة</h3>
                        <p className="text-[10px] text-gray-400 mt-1">اختر المواد التي سيتم تسجيل الطالب بها جزئياً</p>
                    </div>
                    {isPartial && (
                        <span className="px-3 py-1 rounded-lg bg-amber-50 text-amber-600 text-[11px] font-bold">
                            المواد المختارة: {selectedSubjectIds.length}
                        </span>
                    )}
                </div>
                
                <DataTable
                    data={tableData}
                    columns={columns}
                    showCheckbox={isPartial}
                    selectedIds={selectedSubjectIds.map(String)}
                    onSelectChange={(ids) => setSelectedSubjectIds(ids.map(Number))}
                    pageSize={20}
                />
            </div>
        </div>
      </div>
    </div>
  );
}
