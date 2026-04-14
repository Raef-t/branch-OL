"use client";

import { useState, useMemo } from "react";
import Breadcrumb from "@/components/common/Breadcrumb";
import SearchableSelect from "@/components/common/SearchableSelect";
import DatePickerSmart from "@/components/common/DatePickerSmart";
import DataTable from "@/components/common/DataTable";
import ActionsRow from "@/components/common/ActionsRow";
import PrintExportActions from "@/components/common/PrintExportActions";
import ChipsList from "@/components/common/ChipsList";
// import SummaryCards from "../components/SummaryCards"; // Temporarily commented out
import { branches, monthlyReportsData, employees } from "../mockData";
import { RotateCcw } from "lucide-react";

export default function MonthlyReportsPage() {
  // Filters State
  const [dateFrom, setDateFrom] = useState("");
  const [dateTo, setDateTo] = useState("");
  const [selectedBranchIds, setSelectedBranchIds] = useState([]);
  const [selectedStudentId, setSelectedStudentId] = useState("");
  const [selectedCourseIds, setSelectedCourseIds] = useState([]);
  const [selectedIds, setSelectedIds] = useState([]);

  // Checkbox State
  const [isCoverChecked, setIsCoverChecked] = useState(false);
  const [isAttendanceChecked, setIsAttendanceChecked] = useState(false);

  const branchOptions = useMemo(() => {
    return branches.map((b) => ({
      key: b.id,
      value: String(b.id),
      label: b.name,
    }));
  }, []);

  const studentOptions = useMemo(() => {
    return [
      { key: "all", value: "", label: "كل الطلاب" },
      ...employees.map((e) => ({
        key: e.id,
        value: String(e.id),
        label: e.name,
      })),
    ];
  }, []);

  const courseOptions = [
    { key: "cs", value: "cs", label: "شباب علمي" },
    { key: "math", value: "math", label: "رياضيات" },
    { key: "arabic", value: "arabic", label: "لغة عربية" },
  ];

  const selectedCourses = useMemo(() => {
    return courseOptions.filter((c) =>
      selectedCourseIds.includes(String(c.value)),
    );
  }, [selectedCourseIds]);

  // Results State (Reactive Filtering)
  const filteredData = useMemo(() => {
    let data = monthlyReportsData;

    // Filter by active Student
    if (selectedStudentId) {
      data = data.filter((row) => String(row.studentId) === selectedStudentId || row.employeeName === (employees.find(e => String(e.id) === selectedStudentId)?.name));
    }

    // Filter by courses
    if (selectedCourseIds.length > 0) {
      data = data.filter((row) => selectedCourseIds.includes(String(row.departmentId)) || selectedCourseIds.includes(row.department));
    }

    // Date filtering (dummy logic for mock objects)
    if (dateFrom || dateTo) {
      // Logic for date range if data had actual timestamps
    }

    return data;
  }, [selectedStudentId, selectedCourseIds, dateFrom, dateTo]);

  const handleReset = () => {
    setDateFrom("");
    setDateTo("");
    setSelectedBranchIds([]);
    setSelectedStudentId("");
    setSelectedCourseIds([]);
    setSelectedIds([]);
    setIsCoverChecked(false);
    setIsAttendanceChecked(false);
  };

  const handleCourseSelect = (id) => {
    if (id && !selectedCourseIds.includes(id)) {
      setSelectedCourseIds([...selectedCourseIds, id]);
    }
  };

  const removeCourse = (item) => {
    setSelectedCourseIds(
      selectedCourseIds.filter((id) => id !== String(item.value)),
    );
  };

  const columns = [
    { header: "اسم الطالب", key: "employeeName" },
    { header: "الفرع", key: "branch" },
    { header: "الدورة", key: "department" },
    { header: "الشهر", key: "month" },
    { header: "أيام الحضور", key: "attendanceDays" },
    { header: "أيام الغياب", key: "absenceDays" },
    { header: "مرات التأخير", key: "delayCount" },
    {
      header: "العمل الإضافي",
      key: "overtimeHours",
      render: (val) => `${val} ساعة`,
    },
    {
      header: "الحالة",
      key: "status",
      render: (val) => (
        <span
          className={`px-3 py-1 rounded-full text-xs font-medium ${
            val === "completed"
              ? "bg-green-100 text-green-700"
              : "bg-yellow-100 text-yellow-700"
          }`}
        >
          {val === "completed" ? "مكتمل" : "قيد المراجعة"}
        </span>
      ),
    },
  ];

  const isAllSelected =
    filteredData.length > 0 && selectedIds.length === filteredData.length;

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
              placeholder="كل الطلاب"
            />
            <SearchableSelect
              label="الدورة"
              options={courseOptions}
              value=""
              onChange={handleCourseSelect}
              placeholder="اختر دورة..."
            />
            <DatePickerSmart
              label="من تاريخ"
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

      {/* ACTION ROW & CHIPS */}
      <div className="p-4 space-y-4">
        <div className="flex justify-between items-center flex-wrap gap-4">
          <div className="flex items-center gap-3">
            <ActionsRow
              showSelectAll
              isAllSelected={isAllSelected}
              onToggleSelectAll={() =>
                setSelectedIds(
                  isAllSelected ? [] : filteredData.map((s) => String(s.id)),
                )
              }
              addLabel="" // No add for reports
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
              data={filteredData}
              selectedIds={selectedIds}
              columns={columns}
              title="تقرير شهري"
              filename="monthly-report"
            />
          </div>
        </div>

        {/* Chips list for courses */}
        <div className="flex flex-col gap-4 border-t pt-4 border-gray-50">
          {selectedCourses.length > 0 && (
            <ChipsList
              items={selectedCourses}
              getLabel={(item) => item.label}
              onRemove={removeCourse}
            />
          )}
        </div>
      </div>

      {/* SUMMARY - Temporarily commented out */}
      {/* <SummaryCards cards={summaryCardsData} /> */}

      {/* DATA TABLE */}
      <DataTable
        data={filteredData}
        columns={columns}
        selectedIds={selectedIds}
        onSelectChange={setSelectedIds}
        pageSize={10}
        emptyMessage="لا توجد بيانات للتقرير في الفلاتر المختارة"
      />
    </div>
  );
}
