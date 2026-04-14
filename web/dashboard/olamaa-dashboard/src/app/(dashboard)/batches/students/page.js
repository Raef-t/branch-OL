"use client";

import { useMemo, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import Breadcrumb from "@/components/common/Breadcrumb";
import SearchableSelect from "@/components/common/SearchableSelect";
import ActionsRow from "@/components/common/ActionsRow";
import DataTable from "@/components/common/DataTable";
import { ArrowRight, BookOpenText, MoveLeft, Trash2 } from "lucide-react";
import { notify } from "@/lib/helpers/toastify";

import {
  useGetBatchStudentsQuery,
  useRemoveBatchStudentMutation,
} from "@/store/services/batchStudentsApi";
import { useGetBatchesQuery } from "@/store/services/batchesApi";
import { useGetStudentsDetailsQuery } from "@/store/services/studentsApi";

import StudentSubjectsView from "../components/StudentSubjectsView";
import TransferStudentModal from "../components/TransferStudentModal";
import DeleteConfirmModal from "@/components/common/DeleteConfirmModal";
import PageSkeleton from "@/components/common/PageSkeleton";

export default function BatchStudentsPage() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const batchName = searchParams.get("batch");
  const branchId = searchParams.get("branch");
  const initialView = searchParams.get("view");

  const [selectedStudentId, setSelectedStudentId] = useState("");
  const [activeStudent, setActiveStudent] = useState(null);
  const [selectedIds, setSelectedIds] = useState([]);
  const [isTransferModalOpen, setIsTransferModalOpen] = useState(false);
  const [transferTargetStudent, setTransferTargetStudent] = useState("");
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [deleteTargetId, setDeleteTargetId] = useState(null);

  // 1. Fetch ALL batches to find the ID of the batchName
  const { data: batchesRes } = useGetBatchesQuery({ per_page: 1000 });
  const allBatches = batchesRes?.data?.batches || batchesRes?.data || [];

  const currentBatch = useMemo(() => {
    if (!batchName) return null;
    return allBatches.find((b) => b.name === batchName);
  }, [allBatches, batchName]);

  const batchId = currentBatch?.id;

  // 2. Fetch Students
  // If we have a batchId, get students for that batch
  const { data: batchStudentsRes, isLoading: isBatchLoading } =
    useGetBatchStudentsQuery(batchId, { skip: !batchId });

  // If we don't have a batchId, we fetch from the general students details (all or branch-scoped)
  const { data: branchStudentsRes, isLoading: isBranchLoading } =
    useGetStudentsDetailsQuery(
      { institute_branch_id: branchId && branchId !== "all" ? branchId : undefined },
      { skip: !!batchId },
    );

  const [removeBatchStudent] = useRemoveBatchStudentMutation();

  const rawStudents = useMemo(() => {
    if (batchId) return batchStudentsRes?.data || [];
    const branchData = branchStudentsRes?.data?.data || branchStudentsRes?.data;
    return Array.isArray(branchData) ? branchData : [];
  }, [batchId, batchStudentsRes, branchStudentsRes]);

  const students = useMemo(() => {
    return rawStudents.map((s) => {
      // Find the batch object for this student
      const studentBatchId = s.batch_id || s.latestBatchStudent?.batch_id || s.latest_batch_student?.batch_id;
      
      const studentBatch = batchId 
        ? currentBatch 
        : (s.latestBatchStudent?.batch || s.latest_batch_student?.batch || allBatches.find(b => String(b.id) === String(studentBatchId)));

      const regDate = s.enrollment_date || s.student?.enrollment_date || s.created_at || s.student?.created_at;
      const formattedRegDate = regDate && !isNaN(new Date(regDate).getTime())
        ? new Date(regDate).toLocaleDateString("ar-EG")
        : "—";

      return {
        ...s,
        // Map API fields to UI fields
        first_name: s.first_name || s.student?.first_name,
        last_name: s.last_name || s.student?.last_name,
        full_name: s.full_name || s.student?.full_name,
        registration_date: formattedRegDate,
        start_date: s.attendance_enrolment || s.student?.enrollment_date || "—",
        subjects_count: s.subjects_count ?? (studentBatch?.batch_subjects?.length || s.batch_subjects_count || 0),
        completion_rate: "0%", 
        batch: studentBatch, // Store the full batch object for the details view
        enrollment_status: s.is_partial ? (
          <span className="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100">
            جزئي
          </span>
        ) : (
          <span className="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100">
            كامل
          </span>
        ),
      };
    });
  }, [rawStudents, batchId, currentBatch, allBatches]);

  const batchOptions = useMemo(() => {
    return allBatches.map((b) => ({
      value: b.name,
      label: b.name,
    }));
  }, [allBatches]);

  const studentOptions = useMemo(() => {
    return [
      { value: "", label: "كل الطلاب" },
      ...students.map((s) => ({
        value: String(s.id),
        label: s.full_name,
      })),
    ];
  }, [students]);

  const filteredStudents = useMemo(() => {
    if (!selectedStudentId) return students;
    return students.filter((s) => String(s.id) === String(selectedStudentId));
  }, [students, selectedStudentId]);

  const columns = [
    { header: "الطالب", key: "full_name" },
    { header: "بدء الدوام", key: "start_date" },
    { header: "تاريخ التسجيل", key: "registration_date" },
    { header: "الحالة", key: "enrollment_status" },
    { header: "المواد", key: "subjects_count" },
  ];

  /* If activeStudent is selected, show details component */
  if (activeStudent) {
    return (
      <StudentSubjectsView
        student={activeStudent}
        batch={activeStudent.batch || currentBatch}
        onBack={() => setActiveStudent(null)}
      />
    );
  }

  if (isBatchLoading || isBranchLoading) {
    return <PageSkeleton />;
  }

  return (
    <div
      dir="rtl"
      className="w-full min-h-screen p-4 md:p-6 bg-[#fcfcfd] flex flex-col gap-6"
    >
      {/* Header */}
      <div className="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
        <div className="flex flex-col gap-1">
          <h1 className="text-xl font-bold text-gray-800">
            {batchName ? `طلاب الدورة: ${batchName}` : "إجمالي طلاب الفرع"}
          </h1>
          <Breadcrumb />
        </div>

        <div className="flex items-center gap-4">
          <SearchableSelect
            label="اسم الطالب"
            value={selectedStudentId}
            onChange={setSelectedStudentId}
            options={studentOptions}
            allowClear
            placeholder="اختر الطالب"
          />
          {/* <button
            onClick={() => router.back()}
            className="h-[40px] px-4 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition flex items-center gap-2"
          >
            <ArrowRight size={16} />
            <span>رجوع</span>
          </button> */}
        </div>
      </div>

      {/* Stats/Actions */}
      <ActionsRow
        viewLabel=""
        addLabel=""
        onAdd={() => {}}
        showSelectAll={true}
        selectedIds={selectedIds}
        isAllSelected={
          filteredStudents.length > 0 &&
          selectedIds.length === filteredStudents.length
        }
        onToggleSelectAll={() =>
          setSelectedIds(
            selectedIds.length === filteredStudents.length
              ? []
              : filteredStudents.map((s) => String(s.id)),
          )
        }
        extraButtons={[
          {
            label: "نقل",
            icon: <MoveLeft size={16} />,
            color: "green",
            onClick: () => {
              if (selectedIds.length === 0) {
                notify.error("يرجى اختيار طلاب للنقل");
                return;
              }
              setTransferTargetStudent(
                selectedIds.length === 1 ? String(selectedIds[0]) : "",
              );
              setIsTransferModalOpen(true);
            },
          },
          {
            label: "حذف",
            icon: <Trash2 size={16} />,
            color: "red",
            onClick: () => {
              if (selectedIds.length === 0) {
                notify.error("يرجى اختيار طلاب للحذف");
                return;
              }
              setDeleteTargetId(null); // Bulk deletion
              setIsDeleteModalOpen(true);
            },
          },
        ]}
      />

      {/* Main Table using DataTable component */}
      <DataTable
        data={filteredStudents}
        columns={columns}
        pageSize={10}
        selectedIds={selectedIds}
        onSelectChange={setSelectedIds}
        onRowClick={(row) => setActiveStudent(row)}
        renderActions={(row) => (
          <div className="flex items-center justify-center gap-3">
            <button
              onClick={(e) => {
                e.stopPropagation();
                router.push(`/batches/students/${row.batch_student_id}/subjects`);
              }}
              className="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
              title="تخصيص المواد (جزئي/كامل)"
            >
              <BookOpenText size={18} />
            </button>
            <button
              onClick={(e) => {
                e.stopPropagation();
                setTransferTargetStudent(String(row.id));
                setIsTransferModalOpen(true);
              }}
              className="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
              title="نقل"
            >
              <MoveLeft size={18} />
            </button>
            <button
              onClick={(e) => {
                e.stopPropagation();
                setDeleteTargetId(String(row.id));
                setIsDeleteModalOpen(true);
              }}
              className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
              title="حذف"
            >
              <Trash2 size={18} />
            </button>
          </div>
        )}
      />

      <TransferStudentModal
        isOpen={isTransferModalOpen}
        onClose={() => {
          setIsTransferModalOpen(false);
          setTransferTargetStudent("");
        }}
        initialBatch={batchName}
        initialStudent={transferTargetStudent}
        batches={batchOptions}
        students={filteredStudents.map((s) => ({
          value: String(s.id),
          label: s.full_name,
        }))}
      />

      <DeleteConfirmModal
        isOpen={isDeleteModalOpen}
        onClose={() => setIsDeleteModalOpen(false)}
        onConfirm={() => {
          if (deleteTargetId) {
            console.log("Deleting row:", deleteTargetId);
            notify.success("تم الحذف بنجاح");
          } else {
            console.log("Bulk deleting:", selectedIds);
            notify.success(`تم حذف ${selectedIds.length} طلاب بنجاح`);
            setSelectedIds([]);
          }
          setIsDeleteModalOpen(false);
        }}
        description={
          deleteTargetId
            ? "هل تريد بالتأكيد حذف هذا الطالب من هذه الشعبة؟"
            : `هل تريد بالتأكيد حذف ${selectedIds.length} طلاب من هذه الشعبة؟`
        }
      />
    </div>
  );
}
