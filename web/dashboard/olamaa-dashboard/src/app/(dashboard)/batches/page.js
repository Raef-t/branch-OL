"use client";

import { useRouter } from "next/navigation";
import { useEffect, useMemo, useState } from "react";
import { useSelector, useDispatch } from "react-redux";
import Image from "next/image";
import {
  BookOpen,
  Users,
  MoveLeft,
  Archive,
  EyeOff,
  Layers3,
  CheckCircle2,
  Sparkles,
} from "lucide-react";

import ActionsRow from "@/components/common/ActionsRow";
import Breadcrumb from "@/components/common/Breadcrumb";
import PrintExportActions from "@/components/common/PrintExportActions";
import SearchableSelect from "@/components/common/SearchableSelect";
import DeleteConfirmModal from "@/components/common/DeleteConfirmModal";
import { notify } from "@/lib/helpers/toastify";

import BatchesTable from "./components/BatchesTable";
import TransferStudentModal from "./components/TransferStudentModal";
import AddBatchModal from "./components/AddBatchModal";

// ===== APIs =====
import {
  useGetBatchesQuery,
  useGetBatchesStatsQuery,
  useDeleteBatchMutation,
  useToggleBatchStatusMutation,
} from "@/store/services/batchesApi";
import { useGetStudentsDetailsQuery } from "@/store/services/studentsApi";

/* ================= Helpers ================= */

const getStatusLabel = (b) => {
  if (b.is_completed) return "مكتملة";
  if (b.is_hidden) return "مخفية";
  if (b.is_archived) return "مؤرشفة";
  return "نشطة";
};

export default function BatchesPage() {
  const router = useRouter();
  const dispatch = useDispatch();

  // Branch from Redux Navbar
  const branchId = useSelector((state) => state.search?.values?.branch);

  const [selectedStudent, setSelectedStudent] = useState("");
  const [selectedBatch, setSelectedBatch] = useState(""); // This stores the Name for filtering
  const [selectedBatchId, setSelectedBatchId] = useState(""); // This stores the ID for transfer/details
  const [selectedIds, setSelectedIds] = useState([]);
  const [activeView, setActiveView] = useState("all"); // all | hidden | archived | completed

  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [editingBatch, setEditingBatch] = useState(null);
  const [isTransferModalOpen, setIsTransferModalOpen] = useState(false);

  const [isDeleteOpen, setIsDeleteOpen] = useState(false);
  const [batchToDelete, setBatchToDelete] = useState(null);

  // APIs
  const { data: batchesRes, isLoading, refetch } = useGetBatchesQuery({
    institute_branch_id: branchId || undefined,
    status: activeView !== "all" ? activeView : undefined,
    student_name: selectedStudent || undefined,
    name: selectedBatch || undefined,
  });

  const { data: statsRes } = useGetBatchesStatsQuery({
    institute_branch_id: branchId || undefined,
  });

  // Query for ALL students in branch for filter options
  const { data: studentsRes } = useGetStudentsDetailsQuery(
    { institute_branch_id: branchId || undefined },
    { skip: !branchId }
  );

  // Query for ALL batches in branch (unfiltered by name/status) for Transfer Modal options
  const { data: allBatchesRes } = useGetBatchesQuery({
    institute_branch_id: branchId || undefined,
    status: "active",
    per_page: 1000,
  });

  const [deleteBatch] = useDeleteBatchMutation();
  const [toggleBatchStatus] = useToggleBatchStatusMutation();

  const batches = batchesRes?.data?.batches || batchesRes?.data || [];
  const allBatchesInBranch = allBatchesRes?.data?.batches || allBatchesRes?.data || [];
  const stats = statsRes?.data || {
    total: 0,
    hidden: 0,
    archived: 0,
    completed: 0,
    active: 0,
  };

  const studentOptions = useMemo(() => {
    const sList = studentsRes?.data?.data || studentsRes?.data || [];
    return sList.map((s) => ({
      value: s.full_name || `${s.first_name} ${s.last_name}`,
      label: s.full_name || `${s.first_name} ${s.last_name}`,
    }));
  }, [studentsRes]);

  const batchOptions = useMemo(() => {
    return allBatchesInBranch.map((b) => ({
      value: b.name,
      label: b.name,
      id: b.id, // Store ID here to extract later
    }));
  }, [allBatchesInBranch]);

  const transferModalBatchOptions = useMemo(() => {
    return allBatchesInBranch.map((b) => ({
      value: b.id,
      label: b.name,
    }));
  }, [allBatchesInBranch]);

  const isAllSelected = batches.length > 0 && selectedIds.length === batches.length;

  useEffect(() => {
    setSelectedIds([]);
  }, [selectedStudent, selectedBatch, activeView, branchId]);

  const firstRow = batches[0] || {};

  const handleDelete = (row) => {
    setBatchToDelete(row);
    setIsDeleteOpen(true);
  };

  const confirmDelete = async () => {
    if (!batchToDelete) return;
    try {
      await deleteBatch(batchToDelete.id).unwrap();
      notify.success("تم حذف الدورة بنجاح");
      setIsDeleteOpen(false);
      setBatchToDelete(null);
      setSelectedIds((prev) =>
        prev.filter((id) => id !== String(batchToDelete.id))
      );
    } catch (err) {
      notify.error(err?.data?.message || "حدث خطأ أثناء الحذف");
    }
  };

  const handleEdit = (id) => {
    const target = batches.find((b) => b.id === id);
    if (target) {
      setEditingBatch(target);
      setIsAddModalOpen(true);
    }
  };

  const handleToggleStatus = async (id, field) => {
    try {
      const res = await toggleBatchStatus({ id, field }).unwrap();
      notify.success(res?.message || "تم التحديث بنجاح");
    } catch (err) {
      notify.error(err?.data?.message || "حدث خطأ أثناء تحديث الحالة");
    }
  };

  return (
    <div
      dir="rtl"
      className="w-full min-h-screen p-4 md:p-6 flex flex-col gap-6 bg-[#fcfcfd]"
    >
      {/* Header */}
      <div className="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
        <div className="flex flex-col gap-1 shrink-0">
          <h1 className="text-xl font-bold text-gray-800">الدورات</h1>
          <Breadcrumb />
        </div>

        <div className="w-full xl:w-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div className="min-w-0 sm:min-w-[230px]">
            <SearchableSelect
              label="اسم الطالب"
              value={selectedStudent}
              onChange={setSelectedStudent}
              options={[{ value: "", label: "كل الطلاب" }, ...studentOptions]}
              allowClear
              placeholder="اختر الطالب"
            />
          </div>

          <div className="min-w-0 sm:min-w-[230px]">
            <SearchableSelect
              label="الشعبة"
              value={selectedBatch}
              onChange={(val) => {
                setSelectedBatch(val);
                // Find and set the ID
                const found = batchOptions.find((opt) => opt.value === val);
                setSelectedBatchId(found ? found.id : "");
              }}
              options={[{ value: "", label: "كل الشعب" }, ...batchOptions]}
              allowClear
              placeholder="اختر الشعبة"
            />
          </div>
        </div>
      </div>

      {/* Actions row */}
      <div className="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div className="w-full xl:w-auto">
          <ActionsRow
            showSelectAll
            viewLabel=""
            addLabel="إضافة دورة"
            isAllSelected={isAllSelected}
            onToggleSelectAll={() =>
              setSelectedIds(isAllSelected ? [] : batches.map((r) => String(r.id)))
            }
            onAdd={() => {
              setEditingBatch(null);
              setIsAddModalOpen(true);
            }}
            extraButtons={[
              {
                label: "عرض الدورات",
                icon: <Layers3 size={15} />,
                color: activeView === "all" ? "green" : "gray",
                onClick: () => setActiveView("all"),
              },
              {
                label: "عرض الدورات المخفية",
                icon: <EyeOff size={15} />,
                color: activeView === "hidden" ? "green" : "gray",
                onClick: () => setActiveView("hidden"),
              },
              {
                label: "عرض الدورات المؤرشفة",
                icon: <Archive size={15} />,
                color: activeView === "archived" ? "green" : "gray",
                onClick: () => setActiveView("archived"),
              },
              {
                label: "الدورات المكتملة",
                icon: <CheckCircle2 size={15} />,
                color: activeView === "completed" ? "green" : "gray",
                onClick: () => setActiveView("completed"),
              },
              {
                label: "الجدولة الذكية",
                icon: <Sparkles size={15} />,
                color: "pink",
                onClick: () => router.push("/scheduler-wizard"),
              },
            ]}
          />
        </div>

        <div className="w-full xl:w-auto flex flex-wrap items-center gap-2">
          <PrintExportActions
            data={batches}
            selectedIds={selectedIds}
            columns={[
              { header: "اسم الشعبة", key: "name" },
              { header: "اسم الطالب", key: "student_name" },
              {
                header: "الفرع",
                key: "institute_branch",
                render: (val) => val?.name || "—",
              },
              {
                header: "الفرع الأكاديمي",
                key: "academic_branch",
                render: (val) => val?.name || "—",
              },
              { header: "تاريخ البداية", key: "start_date" },
              { header: "تاريخ النهاية", key: "end_date" },
              {
                header: "الحالة",
                key: "id",
                render: (_, row) => getStatusLabel(row),
              },
            ]}
            title="قائمة الشعب"
            filename="الشعب"
          />

          <button
            type="button"
            onClick={() => {
              if (!selectedBatchId) {
                notify.error("يرجى اختيار شعبة من الفلترة للمتابعة");
                return;
              }
              setIsTransferModalOpen(true);
            }}
            className="h-[38px] px-4 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition inline-flex items-center gap-2"
          >
            <MoveLeft size={16} />
            <span>نقل</span>
          </button>
        </div>
      </div>

      {/* Stats + cards */}
      <div className="grid grid-cols-1 2xl:grid-cols-12 gap-4">
        <div className="2xl:col-span-10 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="bg-[#FAF5FD] border border-[#F1E4F6] p-5 rounded-[24px] shadow-sm relative overflow-hidden min-h-[190px] flex">
            {/* Bottom-left white curve & button */}
            <div className="absolute -bottom-8 -left-8 w-36 h-36 bg-white rounded-full"></div>
            <button
              onClick={() => {
                if (!selectedBatch) return;
                if (batches.length === 1) {
                  router.push(`/batches/batch-subjects?batch=${batches[0].name}`);
                } else {
                  router.push(`/subjects`);
                }
              }}
              disabled={!selectedBatch}
              className={`absolute bottom-4 left-4 w-14 h-14 rounded-full bg-[#8A1654] text-white flex items-center justify-center transition text-2xl shadow-md z-20 ${
                !selectedBatch ? "opacity-30 cursor-not-allowed" : "hover:bg-[#741046]"
              }`}
            >
              ↗
            </button>

            {/* Right Group: Icon, Text, Avatars (Appears on Right in RTL) */}
            <div className="relative z-10 flex-1 flex flex-col items-start text-right">
              <div className="w-12 h-12 rounded-xl bg-[#8A1654] text-white flex items-center justify-center shrink-0 mb-3 shadow-[0_4px_10px_rgba(138,22,84,0.3)]">
                <BookOpen size={22} />
              </div>
              <h3 className="text-xl font-bold text-gray-900 mb-1">
                {batches.length === 1 ? "مواد الدورة" : "إجمالي المواد"}
              </h3>
              <p className="text-sm text-gray-500 mb-4 max-w-[200px] leading-relaxed">
                {batches.length === 1
                  ? `عرض المواد الدراسية الخاصة بـ ${batches[0].name}`
                  : "عرض جميع المواد الدراسية في هذا الفرع"}
              </p>
            </div>

            {/* Left Group: Number (Appears on Left in RTL) */}
            <div className="relative z-10 flex flex-col justify-start items-center w-24 shrink-0">
              <div className="text-3xl font-bold text-gray-900 mt-6">
                {batches.length === 1
                  ? (batches[0].subjects_count || 0)
                  : (stats.total_subjects || 0)}
              </div>
            </div>
          </div>

          <div className="bg-[#FAF5FD] border border-[#F1E4F6] p-5 rounded-[24px] shadow-sm relative overflow-hidden min-h-[190px] flex">
            {/* Bottom-left white curve & button */}
            <div className="absolute -bottom-8 -left-8 w-36 h-36 bg-white rounded-full"></div>
            <button
              onClick={() => {
                if (!selectedBatch) return;
                if (batches.length === 1) {
                  router.push(`/batches/students?batch=${batches[0].name}`);
                } else {
                  router.push(`/batches/students?batch=${selectedBatch}`);
                }
              }}
              disabled={!selectedBatch}
              className={`absolute bottom-4 left-4 w-14 h-14 rounded-full bg-[#8A1654] text-white flex items-center justify-center transition text-2xl shadow-md z-20 ${
                !selectedBatch ? "opacity-30 cursor-not-allowed" : "hover:bg-[#741046]"
              }`}
            >
              ↗
            </button>

            {/* Right Group: Icon, Text, Avatars (Appears on Right in RTL) */}
            <div className="relative z-10 flex-1 flex flex-col items-start text-right">
              <div className="w-12 h-12 rounded-xl bg-[#8A1654] text-white flex items-center justify-center shrink-0 mb-3 shadow-[0_4px_10px_rgba(138,22,84,0.3)]">
                <Users size={22} />
              </div>
              <h3 className="text-xl font-bold text-gray-900 mb-1">
                {batches.length === 1 ? "طلاب الدورة" : "إجمالي الطلاب"}
              </h3>
              <p className="text-sm text-gray-500 mb-4 max-w-[200px] leading-relaxed">
                {batches.length === 1
                  ? `عرض الطلاب المسجلين في ${batches[0].name}`
                  : "عرض جميع الطلاب المسجلين في هذا الفرع"}
              </p>
            </div>

            {/* Left Group: Number (Appears on Left in RTL) */}
            <div className="relative z-10 flex flex-col justify-start items-center w-24 shrink-0">
              <div className="text-3xl font-bold text-gray-900 mt-6">
                {batches.length === 1
                  ? (batches[0].students_count || 0)
                  : (stats.total_students || 0)}
              </div>
            </div>
          </div>
        </div>

        {/* small stat cards */}
        <div className="2xl:col-span-2 grid grid-cols-1 sm:grid-cols-2 2xl:grid-cols-1 gap-3">
          <div
            className="flex flex-col justify-between rounded-2xl bg-white px-5 py-4 shadow-sm relative overflow-hidden min-h-[95px]"
            style={{
              background:
                "radial-gradient(120px 120px at 90% 10%, rgba(16,163,69,0.15), transparent 60%)",
            }}
          >
            <div className="flex items-center justify-between gap-3">
              <div className="text-xl font-semibold text-gray-900">
                {stats.completed}{" "}
                <span className="text-base font-semibold">دورة</span>
              </div>
              <Image
                src="/greenGlobe.svg"
                alt="completed"
                width={20}
                height={20}
              />
            </div>
            <div className="mt-2 text-sm text-gray-500">مكتملة</div>
          </div>

          <div
            className="flex flex-col justify-between rounded-2xl bg-white px-5 py-4 shadow-sm relative overflow-hidden min-h-[95px]"
            style={{
              background:
                "radial-gradient(120px 120px at 90% 10%, rgba(244,114,182,0.14), transparent 60%)",
            }}
          >
            <div className="flex items-center justify-between gap-3">
              <div className="text-xl font-semibold text-gray-900">
                {stats.active || 0}{" "}
                <span className="text-base font-semibold">دورة</span>
              </div>
              <div className="w-8 h-8 rounded-full bg-[#FCE7F3] flex items-center justify-center text-[#BE185D] font-bold text-sm">
                +
              </div>
            </div>
            <div className="mt-2 text-sm text-gray-500">نشطة</div>
          </div>
        </div>
      </div>

      {/* Table title */}
      <div className="flex items-center justify-between">
        <h3 className="text-lg font-bold text-gray-700">
          معلومات الدورة ({batches.length})
        </h3>
      </div>

      {/* Table */}
      <BatchesTable
        batches={batches}
        isLoading={isLoading}
        selectedIds={selectedIds}
        onSelectChange={setSelectedIds}
        onEdit={handleEdit}
        onDelete={handleDelete}
        onToggleStatus={handleToggleStatus}
        activeView={activeView}
      />

      <AddBatchModal
        isOpen={isAddModalOpen}
        onClose={() => {
          setIsAddModalOpen(false);
          setEditingBatch(null);
          refetch();
        }}
        batch={editingBatch}
      />

      <TransferStudentModal
        isOpen={isTransferModalOpen}
        onClose={() => setIsTransferModalOpen(false)}
        initialBatchId={selectedBatchId}
        batches={transferModalBatchOptions}
      />

      <DeleteConfirmModal
        isOpen={isDeleteOpen}
        loading={isLoading} // Or use a specific isDeleting state if available
        title="حذف دورة"
        description={`هل أنت متأكد من حذف الدورة ${batchToDelete?.name || ""}؟`}
        onClose={() => {
          setIsDeleteOpen(false);
          setBatchToDelete(null);
        }}
        onConfirm={confirmDelete}
      />
    </div>
  );
}
