"use client";

import { useState, useMemo, useEffect } from "react";
import { useSelector } from "react-redux";
import Breadcrumb from "@/components/common/Breadcrumb";
import SearchableSelect from "@/components/common/SearchableSelect";
import DataTable from "@/components/common/DataTable";
import ActionsRow from "@/components/common/ActionsRow";
import PrintExportActions from "@/components/common/PrintExportActions";
import ChipsList from "@/components/common/ChipsList";
import DashboardButton from "@/components/common/DashboardButton";
import { ListFilter, RotateCcw } from "lucide-react";
import { useGetBusesQuery } from "@/store/services/busesApi";
import { useGetBatchesQuery } from "@/store/services/batchesApi";
import { useGetStudentsForReportQuery, useLazyGenerateBusReportQuery } from "@/store/services/reportsApi";

export default function BranchBusesPage() {
  // 1. Global State
  const branchId = useSelector((s) => s.search.values.branch || "");

  // 2. Filters State
  const [selectedStudentId, setSelectedStudentId] = useState("");
  const [selectedBuses, setSelectedBuses] = useState([]); // Array of { id, name }
  const [selectedIds, setSelectedIds] = useState([]);

  // Checkbox State
  const [isCoverChecked, setIsCoverChecked] = useState(false);
  const [isAttendanceChecked, setIsAttendanceChecked] = useState(false);

  // 3. API Queries
  const { data: busesRes, isLoading: loadingBuses } = useGetBusesQuery({
    institute_branch_id: branchId || undefined,
    per_page: 1000,
  });

  const busIdsForFilter = useMemo(() => selectedBuses.map(b => b.id), [selectedBuses]);

  const { data: studentsRes, isFetching: loadingStudents } = useGetStudentsForReportQuery(
    {
      institute_branch_id: branchId || undefined,
      // We can also filter students for the dropdown if needed, but usually we show all if no bus is selected
    }
  );

  const [triggerGenerate, { data: reportRes, isFetching: generating }] = useLazyGenerateBusReportQuery();

  // 4. Synchronization Hooks
  useEffect(() => {
    setSelectedBuses([]);
    setSelectedStudentId("");
    setSelectedIds([]);
  }, [branchId]);

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

  const busOptions = useMemo(() => {
    const rawData = busesRes?.data;
    const arr = Array.isArray(rawData) ? rawData : (Array.isArray(rawData?.data) ? rawData.data : []);
    return arr.map((b) => ({ key: b.id, value: String(b.id), label: b.name }));
  }, [busesRes]);

  const reportData = useMemo(() => Array.isArray(reportRes?.data) ? reportRes.data : [], [reportRes]);

  const handleReset = () => {
    setSelectedStudentId("");
    setSelectedBuses([]);
    setSelectedIds([]);
    setIsCoverChecked(false);
    setIsAttendanceChecked(false);
  };

  const handleAddBus = (id) => {
    const opt = busOptions.find(o => String(o.value) === String(id));
    if (opt && !selectedBuses.find(b => String(b.id) === String(id))) {
      setSelectedBuses([...selectedBuses, { id: opt.value, name: opt.label }]);
    }
  };

  const removeBus = (item) => {
    setSelectedBuses(selectedBuses.filter((b) => String(b.id) !== String(item.id)));
  };

  const handleGenerate = () => {
    triggerGenerate({
      institute_branch_id: branchId || undefined,
      bus_ids: busIdsForFilter.length > 0 ? busIdsForFilter : undefined,
      student_id: selectedStudentId || undefined,
    });
  };

  const columns = [
    { header: "الطالب", key: "employeeName" },
    { header: "الكنية", key: "surname" },
    {
      header: "الحالة",
      key: "status",
      render: (val) => (
        <span
          className={`px-3 py-1 rounded-full text-xs font-medium ${
            val === "مسجل"
              ? "bg-green-100 text-green-700"
              : "bg-gray-100 text-gray-700"
          }`}
        >
          {val}
        </span>
      ),
    },
    { header: "اسم الباص", key: "busName" },
    { header: "رقم الباص", key: "busNumber" },
    { header: "الفرع", key: "branch_name" },
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
              label="الباص"
              options={busOptions}
              value=""
              onChange={handleAddBus}
              placeholder={loadingBuses ? "جاري التحميل..." : "اختر باص..."}
              disabled={loadingBuses}
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

          <div className="flex items-center gap-2">
            <PrintExportActions
              data={reportData}
              selectedIds={selectedIds}
              columns={columns}
              title="تقرير باصات الشعبة"
              filename="branch-buses-report"
            />
          </div>
        </div>

        {/* Selected Buses Chips */}
        <div className="flex flex-col gap-4 border-t pt-4 border-gray-50">
          {selectedBuses.length > 0 && (
            <ChipsList
              items={selectedBuses}
              getLabel={(item) => item.name}
              onRemove={removeBus}
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
