"use client";

import { useState, useMemo, useEffect } from "react";
import { useSelector } from "react-redux";
import Image from "next/image";
import Breadcrumb from "@/components/common/Breadcrumb";
import SearchableSelect from "@/components/common/SearchableSelect";
import DatePickerSmart from "@/components/common/DatePickerSmart";
import DataTable from "@/components/common/DataTable";
import ActionsRow from "@/components/common/ActionsRow";
import PrintExportActions from "@/components/common/PrintExportActions";
import ChipsList from "@/components/common/ChipsList";
import DashboardButton from "@/components/common/DashboardButton";
import { ListFilter, RotateCcw } from "lucide-react";
import { useGetBatchesQuery } from "@/store/services/batchesApi";
import { useGetStudentsForReportQuery, useLazyGenerateExamsReportQuery } from "@/store/services/reportsApi";

export default function ExamsReportPage() {
  // 1. Global State
  const branchId = useSelector((s) => s.search.values.branch || "");

  // 2. Filters State
  const [dateFrom, setDateFrom] = useState("");
  const [dateTo, setDateTo] = useState("");
  const [selectedStudentId, setSelectedStudentId] = useState("");
  const [selectedBatches, setSelectedBatches] = useState([]); // Array of { id, name }
  const [selectedIds, setSelectedIds] = useState([]);

  // Checkbox State
  const [isCoverChecked, setIsCoverChecked] = useState(false);
  const [isAttendanceChecked, setIsAttendanceChecked] = useState(false);

  // 3. API Queries
  const { data: batchesRes, isLoading: loadingBatches } = useGetBatchesQuery({
    institute_branch_id: branchId || undefined,
    per_page: 1000,
  });

  const batchIdsForStudents = useMemo(() => selectedBatches.map(b => b.id), [selectedBatches]);

  const { data: studentsRes, isFetching: loadingStudents } = useGetStudentsForReportQuery(
    {
      institute_branch_id: branchId || undefined,
      batch_ids: batchIdsForStudents.length > 0 ? batchIdsForStudents : undefined,
    }
  );

  const [triggerGenerate, { data: reportRes, isFetching: generating }] = useLazyGenerateExamsReportQuery();

  // 4. Synchronization Hooks
  useEffect(() => {
    setSelectedBatches([]);
    setSelectedStudentId("");
    setSelectedIds([]);
  }, [branchId]);

  useEffect(() => {
    setSelectedStudentId("");
  }, [batchIdsForStudents]);

  const studentOptions = useMemo(() => {
    const arr = Array.isArray(studentsRes?.data) ? studentsRes.data : [];
    return [
      { key: "all", value: "", label: "كل الطلاب" },
      ...arr.map((s) => ({
        key: s.id,
        value: String(s.id),
        label: s.full_name,
      })),
    ];
  }, [studentsRes]);

  const batchOptions = useMemo(() => {
    const rawData = batchesRes?.data;
    const arr = Array.isArray(rawData) ? rawData : (Array.isArray(rawData?.batches) ? rawData.batches : []);
    return arr.map((b) => ({ key: b.id, value: String(b.id), label: b.name }));
  }, [batchesRes]);

  // Results & Stats
  const reportData = useMemo(() => Array.isArray(reportRes?.data) ? reportRes.data : [], [reportRes]);
  const stats = useMemo(() => {
    return reportRes?.stats || { received: 0, notReceived: 0 };
  }, [reportRes]);

  const handleReset = () => {
    setDateFrom("");
    setDateTo("");
    setSelectedStudentId("");
    setSelectedBatches([]);
    setSelectedIds([]);
    setIsCoverChecked(false);
    setIsAttendanceChecked(false);
  };

  const handleAddBatch = (id) => {
    const opt = batchOptions.find(o => String(o.value) === String(id));
    if (opt && !selectedBatches.find(b => String(b.id) === String(id))) {
      setSelectedBatches([...selectedBatches, { id: opt.value, name: opt.label }]);
    }
  };

  const removeBatch = (item) => {
    setSelectedBatches(selectedBatches.filter((b) => String(b.id) !== String(item.id)));
  };

  const handleGenerate = () => {
    triggerGenerate({
      institute_branch_id: branchId || undefined,
      batch_ids: batchIdsForStudents.length > 0 ? batchIdsForStudents : undefined,
      student_id: selectedStudentId || undefined,
      start_date: dateFrom || undefined,
      end_date: dateTo || undefined,
    });
  };

  const columns = [
    { header: "الطالب", key: "employeeName" },
    { header: "الكنية", key: "surname" },
    { header: "اسم المذاكرة", key: "examName" },
    { header: "العلامة", key: "grade" },
    {
      header: "الحالة",
      key: "status",
      render: (val) => (
        <span
          className={`px-3 py-1 rounded-full text-xs font-medium ${
            val === "مستلمة"
              ? "bg-green-100 text-green-700"
              : "bg-red-100 text-red-700"
          }`}
        >
          {val}
        </span>
      ),
    },
    { header: "التاريخ", key: "date" },
  ];

  const isAllSelected = reportData.length > 0 && selectedIds.length === reportData.length;

  return (
    <div dir="rtl" className="p-6 space-y-6">
      {/* HEADER SECTION */}
      <div className="flex flex-col md:flex-row justify-between items-start">
        <div className="mb-4 md:mb-0">
          <h1 className="text-xl font-bold text-gray-800">التقارير</h1>
          <Breadcrumb />
        </div>
        <div className="w-full md:w-auto">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
            <SearchableSelect
              label="اسم الطالب"
              options={studentOptions}
              value={selectedStudentId}
              onChange={setSelectedStudentId}
              placeholder={loadingStudents ? "جاري التحميل..." : "كل الطلاب"}
              disabled={loadingStudents}
            />
            <SearchableSelect
              label="الشعبة"
              options={batchOptions}
              value=""
              onChange={handleAddBatch}
              placeholder="اختر دورة..."
            />
            <DatePickerSmart
              label="من تاريخ المذاكرة"
              value={dateFrom}
              onChange={setDateFrom}
            />
            <DatePickerSmart
              label="حتى تاريخ"
              value={dateTo}
              onChange={setDateTo}
            />
          </div>
        </div>
      </div>

      {/* GENERATE BUTTON SECTION */}
      <div className="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex justify-center sm:justify-end">
         <DashboardButton
           color="primary"
           className="px-12 py-3 rounded-lg text-lg transition-all active:scale-95"
           onClick={handleGenerate}
           loading={generating}
           icon={<ListFilter size={20} />}
           label="توليد التقرير"
         />
      </div>

      {/* ACTION ROW & CHIPS */}
      <div className="p-4 space-y-4">
        <div className="flex justify-between items-center flex-wrap gap-4">
          <div className="flex items-center gap-3">
            <ActionsRow
              showSelectAll
              isAllSelected={isAllSelected}
              onToggleSelectAll={() =>
                setSelectedIds(
                  isAllSelected ? [] : reportData.map((s) => String(s.id)),
                )
              }
              addLabel=""
              viewLabel=""
              showCoverCheckbox
              isCoverChecked={isCoverChecked}
              onCoverChange={setIsCoverChecked}
              showAttendanceCheckbox
              isAttendanceChecked={isAttendanceChecked}
              onAttendanceChange={setIsAttendanceChecked}
            />

            <button
              onClick={handleReset}
              className="flex items-center gap-2 text-gray-500 hover:text-gray-800 transition text-sm"
              title="إعادة تعيين"
            >
              <RotateCcw size={16} />
              مسح الفلاتر
            </button>
          </div>

          <div className="flex flex-col items-end gap-4">
            <div className="flex items-center gap-2">
              <PrintExportActions
                data={reportData}
                selectedIds={selectedIds}
                columns={columns}
                title="تقرير المذاكرات"
                filename="exams-report"
              />
            </div>

            <div className="flex items-center gap-3">
              {/* Received Card */}
              <div
                className="flex items-center gap-3 bg-white border border-gray-100 rounded-2xl px-4 py-1.5 shadow-sm min-w-[130px] h-[60px] relative overflow-hidden"
                style={{
                  background:
                    "linear-gradient(to left, rgba(16,163,69,0.05), transparent)",
                }}
              >
                <div className="flex items-center justify-center w-8 h-8 rounded-full bg-green-50 shrink-0">
                  <Image
                    src="/greenGlobe.svg"
                    alt="received"
                    width={16}
                    height={16}
                  />
                </div>
                <div className="flex flex-col">
                  <div className="text-[15px] font-bold text-gray-900 leading-none">
                    {stats.received}{" "}
                    <span className="text-xs font-semibold">مستلمة</span>
                  </div>
                  <div className="text-[10px] text-gray-400 mt-0.5 whitespace-nowrap">
                    إجمالي المستلمة
                  </div>
                </div>
              </div>

              {/* Not Received Card */}
              <div
                className="flex items-center gap-3 bg-white border border-gray-100 rounded-2xl px-4 py-1.5 shadow-sm min-w-[130px] h-[60px] relative overflow-hidden"
                style={{
                  background:
                    "linear-gradient(to left, rgba(239,68,68,0.05), transparent)",
                }}
              >
                <div className="flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 font-bold text-xs shrink-0">
                  !
                </div>
                <div className="flex flex-col">
                  <div className="text-[15px] font-bold text-gray-900 leading-none">
                    {stats.notReceived}{" "}
                    <span className="text-xs font-semibold">غير مستلمة</span>
                  </div>
                  <div className="text-[10px] text-gray-400 mt-0.5 whitespace-nowrap">
                    غير المستلمة
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Selected Batches Chips */}
        <div className="flex flex-col gap-4 border-t pt-4 border-gray-50">
          {selectedBatches.length > 0 && (
            <ChipsList
              items={selectedBatches}
              getLabel={(item) => item.name}
              onRemove={removeBatch}
            />
          )}
        </div>
      </div>

      {/* DATA TABLE */}
      <DataTable
        data={reportData}
        columns={columns}
        selectedIds={selectedIds}
        onSelectChange={setSelectedIds}
        getRowId={(row) => row.id}
        isLoading={generating}
        pageSize={10}
        emptyMessage="لا توجد بيانات للتقرير في الفلاتر المختارة"
        className="attendance-table"
      />

      <style jsx global>{`
        .attendance-table thead tr {
          background-color: #F9E8F0 !important;
        }
        .attendance-table thead th {
          color: #6F013F !important;
          font-weight: 600 !important;
        }
      `}</style>
    </div>
  );
}
