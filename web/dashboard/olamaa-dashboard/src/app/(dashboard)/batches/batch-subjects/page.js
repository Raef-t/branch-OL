"use client";

import { useMemo, useState, useEffect } from "react";
import { useSelector } from "react-redux";
import { useRouter, useSearchParams } from "next/navigation";
import { BookOpen, Edit, Trash2, ArrowRight } from "lucide-react";

import PageSkeleton from "@/components/common/PageSkeleton";
import DataTable from "@/components/common/DataTable";
import Breadcrumb from "@/components/common/Breadcrumb";
import PrintExportActions from "@/components/common/PrintExportActions";
import ActionsRow from "@/components/common/ActionsRow";

import AddBatchSubjectModal from "../components/AddBatchSubjectModal";
import { useGetBatchSubjectsQuery, useDeleteBatchSubjectMutation } from "@/store/services/batcheSubjectsApi";
import { notify } from "@/lib/helpers/toastify";

// Mock data removed

export default function BatchSubjectsPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const batchId = searchParams.get("id");
  const batchName = searchParams.get("batch") || "علمي بنات";

  const { data: batchSubjectsRes, isLoading, refetch } = useGetBatchSubjectsQuery(batchId, { skip: !batchId });
  const [deleteBatchSubject] = useDeleteBatchSubjectMutation();

  const navSearch = useSelector((s) => s.search?.values?.subjects || "");
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedSubject, setSelectedSubject] = useState(null);
  const [selectedIds, setSelectedIds] = useState([]);

  const batchSubjects = useMemo(() => batchSubjectsRes?.data || [], [batchSubjectsRes]);

  const filteredData = useMemo(() => {
    const q = (navSearch || "").trim().toLowerCase();
    if (!q) return batchSubjects;
    return batchSubjects.filter((s) => 
      String(s.subject_name || "").toLowerCase().includes(q) ||
      String(s.instructor_name || "").toLowerCase().includes(q)
    );
  }, [navSearch, batchSubjects]);

  useEffect(() => {
    setSelectedIds([]);
  }, [navSearch]);

  const columns = [
    { 
      header: "المادة", 
      key: "subject_name",
      render: (v) => <span className="font-bold text-gray-800">{v}</span>
    },
    { 
      header: "المدرس", 
      key: "instructor_name",
      render: (v) => v === "غير محدد" ? (
        <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-medium border border-orange-100">
          <span className="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse" />
          بانتظار تعيين مدرس
        </span>
      ) : v
    },
    { header: "الحصص الأسبوعية", key: "weekly_lessons" },
    {
      header: "تكرار يومي",
      key: "allow_same_subject_same_day",
      render: (v) => (v ? "مسموح" : "غير مسموح")
    },
    { header: "أقصى حصص/يوم", key: "max_lessons_per_day", render: (v) => v || "—" },
  ];

  if (isLoading) {
    return (
      <PageSkeleton 
        tableHeaders={["المادة", "المدرس", "العلامة العظمى", "العلامة الدنيا", "المذاكرات"]}
        rows={8}
      />
    );
  }

  const handleEdit = (subject) => {
    setSelectedSubject(subject);
    setIsModalOpen(true);
  };

  const handleAdd = () => {
    setSelectedSubject(null);
    setIsModalOpen(true);
  };

  return (
    <div
      dir="rtl"
      className="w-full min-h-screen p-4 md:p-6 bg-[#fcfcfd] flex flex-col gap-6"
    >
      {/* Header */}
      <div className="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
        <div className="flex flex-col gap-1">
          <h1 className="text-xl font-bold text-gray-800">
            مواد الدورة: {batchName}
          </h1>
          <Breadcrumb />
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={() => router.back()}
            className="h-[40px] px-4 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition flex items-center gap-2"
          >
            <ArrowRight size={16} />
            <span>رجوع</span>
          </button>
        </div>
      </div>

      {/* Actions */}
      <div className="flex flex-wrap items-center justify-between gap-4">
        <ActionsRow
          addLabel=""
          onAdd={null}
          viewLabel=""
          showSelectAll={true}
          isAllSelected={filteredData.length > 0 && selectedIds.length === filteredData.length}
          onToggleSelectAll={() =>
            setSelectedIds(
              selectedIds.length === filteredData.length
                ? []
                : filteredData.map((s) => String(s.id)),
            )
          }
        />

        <div className="flex items-center gap-2">
          <PrintExportActions
            data={filteredData}
            selectedIds={selectedIds}
            columns={[
              { header: "#", key: "id" },
              { header: "المادة", key: "subject_name" },
              { header: "المدرس", key: "teacher_name" },
              { header: "العلامة العظمى", key: "max_mark" },
              { header: "العلامة الدنيا", key: "min_mark" },
              { header: "المذاكرات", key: "quizzes_count" },
            ]}
            title={`مواد دورة ${batchName}`}
            filename={`مواد-${batchName}`}
          />
        </div>
      </div>

      {/* Table */}
      <DataTable
        data={filteredData}
        columns={columns}
        pageSize={10}
        selectedIds={selectedIds}
        onSelectChange={setSelectedIds}
        renderActions={(row) => (
          <div className="flex items-center justify-center gap-3">
            <button
              onClick={() => handleEdit(row)}
              className="text-[#22C55E] hover:opacity-70 transition p-1"
              title="تعديل"
            >
              <Edit size={18} />
            </button>
            <button
              onClick={async () => {
                if (window.confirm("هل أنت متأكد من حذف هذه المادة من الدورة؟")) {
                  try {
                    await deleteBatchSubject(row.id).unwrap();
                    notify.success("تم حذف المادة بنجاح");
                    refetch();
                  } catch (err) {
                    notify.error("حدث خطأ أثناء الحذف");
                  }
                }
              }}
              className="text-[#EF4444] hover:opacity-70 transition p-1"
              title="حذف"
            >
              <Trash2 size={18} />
            </button>
          </div>
        )}
      />

      {/* Add/Edit Modal */}
      <AddBatchSubjectModal
        isOpen={isModalOpen}
        onClose={() => {
          setIsModalOpen(false);
          refetch();
        }}
        editingSubject={selectedSubject}
        batchId={batchId}
        batchName={batchName}
      />
    </div>
  );
}
