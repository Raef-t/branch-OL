"use client";

import { useState, useMemo, useEffect } from "react";
import { useSelector } from "react-redux";
import { ListFilter, Printer, Download, RotateCcw } from "lucide-react";
import Breadcrumb from "@/components/common/Breadcrumb";
import SearchableSelect from "@/components/common/SearchableSelect";
import DatePickerSmart from "@/components/common/DatePickerSmart";
import DataTable from "@/components/common/DataTable";
import PrintExportActions from "@/components/common/PrintExportActions";
import ActionsRow from "@/components/common/ActionsRow";
import DashboardButton from "@/components/common/DashboardButton";
import ChipsList from "@/components/common/ChipsList";
import PageSkeleton from "@/components/common/PageSkeleton";
import { useGetBatchesQuery } from "@/store/services/batchesApi";
import { useGetStudentsForReportQuery, useLazyGenerateAttendanceReportQuery } from "@/store/services/reportsApi";
import { notify } from "@/lib/helpers/toastify";

export default function AttendanceReportPage() {
  // 1. Global State
  const branchId = useSelector((s) => s.search.values.branch || "");

  // 2. Local Filter State
  const [selectedBatches, setSelectedBatches] = useState([]); // Array of { id, name }
  const [selectedStudentId, setSelectedStudentId] = useState("");
  const [startDate, setStartDate] = useState(new Date().toISOString().split("T")[0]);
  const [endDate, setEndDate] = useState(new Date().toISOString().split("T")[0]);

  // 3. UI Toggles
  const [isAllSelected, setIsAllSelected] = useState(false);
  const [isCoverChecked, setIsCoverChecked] = useState(false);
  const [isAttendanceChecked, setIsAttendanceChecked] = useState(false);
  const [selectedIds, setSelectedIds] = useState([]);

  // 4. API Queries
  const { data: batchesRes, isLoading: loadingBatches } = useGetBatchesQuery({
    institute_branch_id: branchId || undefined,
    per_page: 1000, // Get all active batches for the dropdown
  });

  const batchIds = useMemo(() => selectedBatches.map(b => b.id), [selectedBatches]);

  const { data: studentsRes, isFetching: loadingStudents } = useGetStudentsForReportQuery(
    {
      institute_branch_id: branchId || undefined,
      batch_ids: batchIds.length > 0 ? batchIds : undefined,
    }
  );

  // 4b. Lazy Query for manual trigger (as requested in notes)
  const [triggerGenerate, { data: reportRes, isFetching: generating }] = useLazyGenerateAttendanceReportQuery();

  const handleGenerate = () => {
    // Check if dates are missing (dates are required by API)
    if (!startDate || !endDate) {
      notify('يرجى تحديد الفترة الزمنية أولاً', 'error');
      return;
    }
    
    triggerGenerate({
      institute_branch_id: branchId || undefined,
      batch_ids: batchIds.length > 0 ? batchIds : undefined,
      student_id: selectedStudentId || undefined,
      start_date: startDate,
      end_date: endDate,
    });
  };

  const reportData = useMemo(() => {
    const raw = Array.isArray(reportRes?.data) ? reportRes.data : [];
    return raw.map(item => ({
      ...item,
      id: `${item.student_id}-${item.date}`
    }));
  }, [reportRes]);

  // 5. Hooks for Cascading Logic (Synchronization)
  // Reset Batches and Student when Branch changes
  useEffect(() => {
    setSelectedBatches([]);
    setSelectedStudentId("");
    setSelectedIds([]);
  }, [branchId]);

  // Reset Student Selection if batches change
  useEffect(() => {
    setSelectedStudentId("");
  }, [batchIds]);

  // 6. Data Transformations & Handlers
  const batchOptions = useMemo(() => {
    // Handle both paginated and direct array formats
    const rawData = batchesRes?.data;
    const arr = Array.isArray(rawData) ? rawData : (Array.isArray(rawData?.batches) ? rawData.batches : []);
    return arr.map((b) => ({ key: b.id, value: String(b.id), label: b.name }));
  }, [batchesRes]);

  const studentOptions = useMemo(() => {
    const arr = Array.isArray(studentsRes?.data) ? studentsRes.data : [];
    return [
      { key: 'all', value: '', label: 'كل الطلاب' },
      ...arr.map((s) => ({ key: s.id, value: String(s.id), label: s.full_name }))
    ];
  }, [studentsRes]);

  const handleAddBatch = (id) => {
    const opt = batchOptions.find(o => String(o.value) === String(id));
    if (opt && !selectedBatches.find(b => String(b.id) === String(id))) {
      setSelectedBatches([...selectedBatches, { id: opt.value, name: opt.label }]);
    }
  };

  const handleRemoveBatch = (batch) => {
    setSelectedBatches(selectedBatches.filter(b => b.id !== batch.id));
  };

  const handleReset = () => {
    setSelectedBatches([]);
    setSelectedStudentId("");
    setStartDate(new Date().toISOString().split("T")[0]);
    setEndDate(new Date().toISOString().split("T")[0]);
    setIsAllSelected(false);
    setIsCoverChecked(false);
    setIsAttendanceChecked(false);
    setSelectedIds([]);
  };

  // 7. Table Configuration
  const columns = [
    { header: "اليوم", key: "day" },
    { header: "التاريخ", key: "date" },
    { header: "اسم الطالب", key: "student_name" },
    { header: "الدورة/الشعبة", key: "batch_name" },
    { 
      header: "التفقُدّ", 
      key: "status",
      render: (val) => {
        const styles = {
          'حاضر': 'bg-[#DEFFE0] text-[#2F8F46]',
          'غائب': 'bg-red-100 text-red-700',
          'متأخر': 'bg-orange-100 text-orange-700',
          'مجاز': 'bg-blue-100 text-blue-700',
          'إذن': 'bg-orange-100 text-orange-700',
        };
        const className = styles[val] || 'bg-gray-100 text-gray-700';
        return (
          <span className={`px-3 py-1 rounded-md text-xs font-medium ${className}`}>
            {val || '—'}
          </span>
        );
      }
    },
    { header: "وقت الوصول", key: "check_in" },
    { header: "وقت الانصراف", key: "check_out" },
  ];

  return (
    <div dir="rtl" className="p-6 space-y-6">
      {/* HEADER SECTION - Matches Standard reporting layout */}
      <div className="flex flex-col md:flex-row justify-between items-start">
        <div className="mb-4 md:mb-0">
          <h1 className="text-xl font-bold text-gray-800">التقارير</h1>
          <Breadcrumb />
        </div>
        <div className="w-full md:w-auto">
          <div className="flex flex-col gap-4">
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
                label="الدورة"
                options={batchOptions}
                value=""
                onChange={handleAddBatch}
                placeholder="اختر دورة..."
              />
              <DatePickerSmart
                label="من تاريخ"
                value={startDate}
                onChange={setStartDate}
              />
              <DatePickerSmart
                label="حتى تاريخ"
                value={endDate}
                onChange={setEndDate}
              />
            </div>
          </div>
        </div>
      </div>

      {/* GENERATE BUTTON SECTION - More prominent now */}
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

      {/* ACTION ROW & CHIPS SECTION */}
      <div className="p-4 space-y-4">
        <div className="flex flex-wrap justify-between items-center gap-4">
           {/* Actions / Checkboxes (Right side in RTL) */}
           <div className="flex items-center gap-3">
              <ActionsRow 
                showSelectAll
                isAllSelected={isAllSelected}
                onToggleSelectAll={() => {
                   setIsAllSelected(!isAllSelected);
                   setSelectedIds(isAllSelected ? [] : reportData.map(r => r.id));
                }}
                showCoverCheckbox
                isCoverChecked={isCoverChecked}
                onCoverChange={setIsCoverChecked}
                showAttendanceCheckbox
                isAttendanceChecked={isAttendanceChecked}
                onAttendanceChange={setIsAttendanceChecked}
                addLabel={null} // Explicitly null to hide
                viewLabel={null} // Explicitly null to hide
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

           {/* Export / Print (Left side in RTL) */}
           <div className="flex items-center gap-2">
              <PrintExportActions
                data={reportData}
                selectedIds={selectedIds}
                columns={columns}
                title={`تقرير الحضور والغياب - ${startDate} إلى ${endDate}`}
                filename="Attendance_Report"
              />
           </div>
        </div>

        {/* Selected Batches Chips */}
        <div className="flex flex-col gap-4 border-t pt-4 border-gray-50">
           {selectedBatches.length > 0 && (
              <ChipsList 
                items={selectedBatches}
                onRemove={handleRemoveBatch}
                getLabel={(b) => b.name}
              />
           )}
        </div>
      </div>

      {/* DATA TABLE SECTION */}
      <DataTable
        columns={columns}
        data={reportData}
        isLoading={generating}
        pageSize={10}
        selectedIds={selectedIds}
        onSelectChange={setSelectedIds}
        getRowId={(row) => row.id}
        emptyMessage="لا توجد بيانات للتقرير في الفلاتر المختارة"
        // Applying the request pink header style
        className="attendance-table"
        renderActions={(row) => (
             <div className="flex items-center justify-center gap-2">
                <button className="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="تعديل">
                   <ListFilter size={16} />
                </button>
                <button className="p-1.5 text-red-600 hover:bg-red-50 rounded" title="حذف">
                   <RotateCcw size={16} />
                </button>
             </div>
        )}
      />

      {/* Persistence of visual aesthetic */}
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
