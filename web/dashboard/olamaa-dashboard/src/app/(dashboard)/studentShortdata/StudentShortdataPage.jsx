"use client";

import { useState, useMemo } from "react";
import { notify } from "@/lib/helpers/toastify";
import * as XLSX from "xlsx";
import { saveAs } from "file-saver";

import useStudentData from "./hooks/useStudentData";

import StudentCard from "./components/StudentCard";
import StudentInfoTab from "./components/tabs/StudentInfoTab";
import AttendanceTab from "./components/tabs/AttendanceTab";
import PaymentsTab from "./components/tabs/PaymentsTab";
import EditAttendanceModal from "./components/EditAttendanceModal";
import StudentShortdataSkeleton from "./components/StudentShortdataSkeleton";

import ExcelButton from "@/components/common/ExcelButton";
import PrintButton from "@/components/common/PrintButton";

import ExportChoiceModal from "@/components/common/ExportChoiceModal";

// RTK
import { useGetAttendanceLogQuery } from "@/store/services/studentAttendanceApi";
import { useGetStudentPaymentsSummaryQuery } from "@/store/services/studentPaymentsApi";
import { useGetFilteredExamResultsQuery } from "@/store/services/examsApi"; // Added
import ExamResultsTab from "./components/tabs/ExamResultsTab";
import PrintExportActions from "@/components/common/PrintExportActions"; // Added

/* ================= Helpers ================= */

function toYMD(date) {
  if (!date) return "";
  const d = date instanceof Date ? date : new Date(date);
  if (Number.isNaN(d.getTime())) return "";
  return d.toLocaleDateString("en-CA");
}

function toYMDFromAny(value) {
  if (!value) return "";
  if (typeof value === "string") return value.slice(0, 10);
  if (value instanceof Date) return toYMD(value);
  return "";
}

function normalizeRange(start, end) {
  const a = toYMD(start);
  const b = toYMD(end);
  if (!a || !b) return { min: "", max: "" };
  return a <= b ? { min: a, max: b } : { min: b, max: a };
}

function filterAttendanceByRange(records, range) {
  if (!range?.start || !range?.end) return records;
  const { min, max } = normalizeRange(range.start, range.end);
  if (!min || !max) return records;
  return records.filter((r) => r?.date && r.date >= min && r.date <= max);
}

function filterPaymentsByRange(payments, range) {
  if (!range?.start || !range?.end) return payments;
  const { min, max } = normalizeRange(range.start, range.end);
  if (!min || !max) return payments;

  return payments.filter((p) => {
    const rawDate =
      p.payment_date || p.date || p.paid_at || p.created_at || p.updated_at;
    const ymd = toYMDFromAny(rawDate);
    if (!ymd) return false;
    return ymd >= min && ymd <= max;
  });
}

function filterExamResultsByRange(results, range) {
  const start = range?.start || null;
  const end = range?.end || null;
  if (!start && !end) return results;

  if (start && !end) {
    const target = toYMD(start);
    return results.filter((item) => {
      const ymd = toYMDFromAny(item.created_at || item.updated_at);
      return ymd && ymd === target;
    });
  }

  if (start && end) {
    const { min, max } = normalizeRange(start, end);
    return results.filter((item) => {
      const ymd = toYMDFromAny(item.created_at || item.updated_at);
      return ymd && ymd >= min && ymd <= max;
    });
  }
  return results;
}

function escapeHtml(str) {
  return String(str ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function getPrimaryPhone(student) {
  const guardians = student?.family?.guardians || [];
  const details = guardians.flatMap((g) => g.contact_details || []);

  const primaryPhone = details.find(
    (c) => c.type === "phone" && c.is_primary && c.full_phone_number,
  );
  if (primaryPhone) return primaryPhone.full_phone_number;

  const anyPrimary = details.find(
    (c) => c.is_primary && (c.full_phone_number || c.value),
  );
  return anyPrimary?.full_phone_number || anyPrimary?.value || "—";
}

/* ================= Component ================= */

export default function StudentShortdataPage({ idFromUrl }) {
  // ===== Data =====
  const { student, loading, error } = useStudentData(idFromUrl);

  // ===== Attendance =====
  const { data: attendanceRecords = [] } = useGetAttendanceLogQuery(
    { id: idFromUrl, range: "all" },
    { skip: !idFromUrl },
  );

  // ===== Payments =====
  const { data: paymentsData } = useGetStudentPaymentsSummaryQuery(idFromUrl, {
    skip: !idFromUrl,
  });

  const paymentsAll =
    paymentsData?.payments || paymentsData?.data?.payments || [];

  // ===== Exams =====
  const { data: examsData } = useGetFilteredExamResultsQuery(
    { student_id: idFromUrl },
    { skip: !idFromUrl },
  );

  const examsAll = useMemo(() => {
    if (Array.isArray(examsData?.data)) return examsData.data;
    if (Array.isArray(examsData)) return examsData;
    return [];
  }, [examsData]);

  // ===== UI =====
  const [activeTab, setActiveTab] = useState("info");

  // Selection states (Lifted)
  const [attendanceSelectedIds, setAttendanceSelectedIds] = useState([]);
  const [paymentsSelectedIds, setPaymentsSelectedIds] = useState([]);
  const [examsSelectedIds, setExamsSelectedIds] = useState([]);

  // Range لكل تبويب
  const [attendanceRange, setAttendanceRange] = useState({
    start: null,
    end: null,
  });

  const [paymentsRange, setPaymentsRange] = useState({
    start: null,
    end: null,
  });

  const [examResultsRange, setExamResultsRange] = useState({
    start: null,
    end: null,
  });

  // موجود فقط للـ compatibility
  const [selectedDate, setSelectedDate] = useState(null);

  // ===== Edit attendance =====
  const [openEdit, setOpenEdit] = useState(false);
  const [recordToEdit, setRecordToEdit] = useState(null);
  const [editTrigger, setEditTrigger] = useState(0);

  // ===== Export Modal (Excel/Print) =====
  const [exportModalOpen, setExportModalOpen] = useState(false);
  const [exportType, setExportType] = useState("excel"); // "excel" | "print"
  const [exportLoading, setExportLoading] = useState(false);

  const attendanceView = useMemo(() => {
    const list = filterAttendanceByRange(attendanceRecords, attendanceRange);
    return list.map((r, i) => ({ ...r, _unique_idx: i }));
  }, [attendanceRecords, attendanceRange]);

  const paymentsView = useMemo(() => {
    return filterPaymentsByRange(paymentsAll, paymentsRange);
  }, [paymentsAll, paymentsRange]);

  const examsView = useMemo(() => {
    return filterExamResultsByRange(examsAll, examResultsRange);
  }, [examsAll, examResultsRange]);

  const activeTabProps = useMemo(() => {
    if (activeTab === "attendance") {
      return {
        selectedIds: attendanceSelectedIds,
        onSelectChange: setAttendanceSelectedIds
      };
    }
    if (activeTab === "payments") {
      return {
        selectedIds: paymentsSelectedIds,
        onSelectChange: setPaymentsSelectedIds
      };
    }
    if (activeTab === "examResults") {
      return {
        selectedIds: examsSelectedIds,
        onSelectChange: setExamsSelectedIds
      };
    }
    return {};
  }, [activeTab, attendanceSelectedIds, paymentsSelectedIds, examsSelectedIds]);

  if (!idFromUrl) {
    return (
      <div className="w-full h-full flex items-center justify-center p-10 text-gray-500">
        لا يوجد معرف طالب في الرابط.
      </div>
    );
  }

  if (loading) return <StudentShortdataSkeleton />;

  if (error || !student) {
    return (
      <div className="p-10 text-center text-red-500">
        لم يتم العثور على بيانات الطالب.
      </div>
    );
  }

  // تحديث range حسب التبويب الحالي
  const handleRangeChange = ({ tab, range }) => {
    if (tab === "payments") {
      setPaymentsRange(range);
      setPaymentsSelectedIds([]);
      return;
    }

    if (tab === "examResults") {
      setExamResultsRange(range);
      setExamsSelectedIds([]);
      return;
    }

    setAttendanceRange(range);
    setAttendanceSelectedIds([]);
  };

  /* ================= Excel ================= */

  const exportWorkbook = (wb, filename) => {
    const buffer = XLSX.write(wb, { bookType: "xlsx", type: "array" });
    saveAs(new Blob([buffer], { type: "application/octet-stream" }), filename);
  };

  const addInfoSheet = (wb) => {
    const studentSheet = XLSX.utils.json_to_sheet([
      {
        "الاسم الكامل": student?.full_name || "—",
        الجنس: student?.gender || "—",
        الهاتف: getPrimaryPhone(student),
        الفرع: student?.institute_branch?.name || "—",
        الشعبة: student?.batch?.name || "—",
        الحالة: student?.status?.name || "—",
        "تاريخ التسجيل": student?.enrollment_date || "—",
        "تاريخ بدء الأقساط": student?.enrollment_contract?.installments_start_date
          ? student.enrollment_contract.installments_start_date.split("T")[0]
          : "—",
      },
    ]);
    XLSX.utils.book_append_sheet(wb, studentSheet, "معلومات الطالب");
  };

  const addAttendanceSheet = (wb) => {
    const attendanceSheet = XLSX.utils.json_to_sheet(
      attendanceView.map((r) => ({
        التاريخ: r.date,
        الوصول: r.check_in || "",
        الانصراف: r.check_out || "",
        الحالة: r.status === "present" ? "حاضر" : r.status === "absent" ? "غائب" : r.status === "late" ? "متأخر" : r.status || "",
      })),
    );
    XLSX.utils.book_append_sheet(wb, attendanceSheet, "الحضور والغياب");
  };

  const addPaymentsSheet = (wb) => {
    const paymentsSheet = XLSX.utils.json_to_sheet(
      paymentsView.map((p) => ({
        "تاريخ الدفع":
          toYMDFromAny(
            p.payment_date ||
              p.date ||
              p.paid_at ||
              p.created_at ||
              p.updated_at,
          ) || "",
        "رقم الإيصال": p.receipt_number || "",
        المبلغ: p.amount_usd ?? p.amount ?? "",
      })),
    );
    XLSX.utils.book_append_sheet(wb, paymentsSheet, "الدفعات");
  };

  const addExamsSheet = (wb) => {
    const examsSheet = XLSX.utils.json_to_sheet(
      examsView.map((e) => ({
        "رقم الامتحان": e.exam_id || "",
        "تاريخ النتيجة": toYMDFromAny(e.created_at) || "",
        "العلامة": e.obtained_marks ?? "",
        "الحالة": Number(e.is_passed) === 1 ? "ناجح" : "راسب",
        "الملاحظات": e.remarks || "",
      })),
    );
    XLSX.utils.book_append_sheet(wb, examsSheet, "النتائج الامتحانية");
  };

  // ✅ Excel: الكل (4 Sheets)
  const handleExcelAll = () => {
    try {
      const wb = XLSX.utils.book_new();
      addInfoSheet(wb);
      addAttendanceSheet(wb);
      addPaymentsSheet(wb);
      addExamsSheet(wb);
      exportWorkbook(wb, `student_${student.id}_ALL.xlsx`);
    } catch (e) {
      console.error(e);
      notify.error("فشل تصدير الإكسل");
    }
  };

  // ✅ Excel: التبويب الحالي فقط (Logic replicated from PrintExportActions for consistency)
  const handleExcelCurrentTab = () => {
    try {
      const wb = XLSX.utils.book_new();

      if (activeTab === "info") {
        addInfoSheet(wb);
        exportWorkbook(wb, `student_${student.id}_INFO.xlsx`);
        return;
      }

      if (activeTab === "attendance") {
        const rows = attendanceSelectedIds.length > 0 
          ? attendanceView.filter(r => {
              const id = r.id || r.uuid || r.record_id || (r._unique_idx !== undefined ? `${r.date}-${r.check_in}-${r.check_out}-${r._unique_idx}` : `${r.date}-${r.check_in}-${r.check_out}`);
              return attendanceSelectedIds.includes(String(id));
            })
          : attendanceView;
        const ws = XLSX.utils.json_to_sheet(rows.map(r => ({
          "التاريخ": r.date,
          "الوصول": r.check_in || "",
          "الانصراف": r.check_out || "",
          "الحالة": r.status === "present" ? "حاضر" : r.status === "absent" ? "غائب" : r.status === "late" ? "متأخر" : r.status || "",
        })));
        XLSX.utils.book_append_sheet(wb, ws, "Attendance");
        exportWorkbook(wb, `student_${student.id}_ATTENDANCE.xlsx`);
        return;
      }

      if (activeTab === "payments") {
        const rows = paymentsSelectedIds.length > 0 
          ? paymentsView.filter(p => paymentsSelectedIds.includes(String(p.id)))
          : paymentsView;
        const ws = XLSX.utils.json_to_sheet(rows.map(p => ({
          "تاريخ الدفع": toYMDFromAny(p.payment_date || p.date || p.paid_at || p.created_at || p.updated_at) || "",
          "رقم الإيصال": p.receipt_number || "",
          "المبلغ": p.amount_usd ?? p.amount ?? "",
        })));
        XLSX.utils.book_append_sheet(wb, ws, "Payments");
        exportWorkbook(wb, `student_${student.id}_PAYMENTS.xlsx`);
        return;
      }

      if (activeTab === "examResults") {
        const rows = examsSelectedIds.length > 0 
          ? examsView.filter(e => examsSelectedIds.includes(String(e.id)))
          : examsView;
        const ws = XLSX.utils.json_to_sheet(rows.map(e => ({
          "رقم الامتحان": e.exam_id || "",
          "تاريخ النتيجة": toYMDFromAny(e.created_at) || "",
          "العلامة": e.obtained_marks ?? "",
          "الحالة": Number(e.is_passed) === 1 ? "ناجح" : "راسب",
          "الملاحظات": e.remarks || "",
        })));
        XLSX.utils.book_append_sheet(wb, ws, "Exams");
        exportWorkbook(wb, `student_${student.id}_EXAMS.xlsx`);
        return;
      }
    } catch (e) {
      console.error(e);
      notify.error("فشل تصدير الإكسل");
    }
  };

  /* ================= Print ================= */

  const openPrintWindow = (html) => {
    const win = window.open("", "", "width=1000,height=800");
    win.document.write(html);
    win.document.close();
    win.focus();
    win.print();
  };

  const attendanceLabel =
    attendanceRange?.start && attendanceRange?.end
      ? `من ${toYMD(attendanceRange.start)} إلى ${toYMD(attendanceRange.end)}`
      : "بدون فلترة";

  const paymentsLabel =
    paymentsRange?.start && paymentsRange?.end
      ? `من ${toYMD(paymentsRange.start)} إلى ${toYMD(paymentsRange.end)}`
      : "بدون فلترة";

  const examsLabel =
    examResultsRange?.start && examResultsRange?.end
      ? `من ${toYMD(examResultsRange.start)} إلى ${toYMD(examResultsRange.end)}`
      : "بدون فلترة";

  // ✅ Print: الكل (4 صفحات)
  const handlePrintAll = () => {
    try {
      const html = `
        <html dir="rtl">
          <head>
            <meta charset="utf-8" />
            <title>student_${escapeHtml(student.id)}</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 16px; }
              h2 { margin: 0 0 10px; color: #6F013F; }
              .muted { color:#666; font-size:12px; margin-bottom:12px; }
              table { width:100%; border-collapse: collapse; margin-top: 10px; }
              th, td { border:1px solid #ccc; padding:8px; font-size:12px; text-align:right; }
              th { background:#f6f6f6; }
              .section { margin-bottom: 24px; }
              .page-break { page-break-after: always; }
              @media print { .page-break { page-break-after: always; } }
            </style>
          </head>
          <body>

            <!-- Page 1: Info -->
            <div class="section">
              <h2>معلومات الطالب</h2>
              <div class="muted">تاريخ الطباعة: ${escapeHtml(toYMD(new Date()))}</div>

              <table>
                <tbody>
                  <tr><th>الاسم</th><td>${escapeHtml(student?.full_name || "—")}</td></tr>
                  <tr><th>الهاتف</th><td>${escapeHtml(getPrimaryPhone(student))}</td></tr>
                  <tr><th>الجنس</th><td>${escapeHtml(student?.gender || "—")}</td></tr>
                  <tr><th>الفرع</th><td>${escapeHtml(student?.institute_branch?.name || "—")}</td></tr>
                  <tr><th>الشعبة</th><td>${escapeHtml(student?.batch?.name || "—")}</td></tr>
                  <tr><th>الحالة</th><td>${escapeHtml(student?.status?.name || "—")}</td></tr>
                  <tr><th>تاريخ التسجيل</th><td>${escapeHtml(student?.enrollment_date || "—")}</td></tr>
                  <tr><th>تاريخ بدء الأقساط</th><td>${escapeHtml(student?.enrollment_contract?.installments_start_date ? student.enrollment_contract.installments_start_date.split("T")[0] : "—")}</td></tr>
                </tbody>
              </table>
            </div>

            <div class="page-break"></div>

            <!-- Page 2: Attendance -->
            <div class="section">
              <h2>الحضور والغياب</h2>
              <div class="muted">${escapeHtml(attendanceLabel)}</div>
              <table>
                <thead>
                  <tr><th>#</th><th>التاريخ</th><th>الوصول</th><th>الانصراف</th><th>الحالة</th></tr>
                </thead>
                <tbody>
                  ${attendanceView.length ? attendanceView.map((r, i) => `
                    <tr>
                      <td>${i + 1}</td>
                      <td>${escapeHtml(r.date)}</td>
                      <td>${escapeHtml(r.check_in || "—")}</td>
                      <td>${escapeHtml(r.check_out || "—")}</td>
                      <td>${escapeHtml(r.status === "present" ? "حاضر" : r.status === "absent" ? "غائب" : r.status === "late" ? "متأخر" : r.status || "—")}</td>
                    </tr>
                  `).join("") : `<tr><td colspan="5" style="text-align:center;color:#777">لا يوجد بيانات</td></tr>`}
                </tbody>
              </table>
            </div>

            <div class="page-break"></div>

            <!-- Page 3: Payments -->
            <div class="section">
              <h2>الدفعات</h2>
              <div class="muted">${escapeHtml(paymentsLabel)}</div>
              <table>
                <thead>
                  <tr><th>#</th><th>تاريخ الدفع</th><th>رقم الإيصال</th><th>المبلغ</th></tr>
                </thead>
                <tbody>
                  ${paymentsView.length ? paymentsView.map((p, i) => `
                    <tr>
                      <td>${i + 1}</td>
                      <td>${escapeHtml(toYMDFromAny(p.payment_date || p.date || p.paid_at || p.created_at || p.updated_at) || "—")}</td>
                      <td>${escapeHtml(p.receipt_number || "—")}</td>
                      <td>${escapeHtml(p.amount_usd ?? p.amount ?? "—")}</td>
                    </tr>
                  `).join("") : `<tr><td colspan="4" style="text-align:center;color:#777">لا يوجد بيانات</td></tr>`}
                </tbody>
              </table>
            </div>

            <div class="page-break"></div>

            <!-- Page 4: Exams -->
            <div class="section">
              <h2>النتائج الامتحانية</h2>
              <div class="muted">${escapeHtml(examsLabel)}</div>
              <table>
                <thead>
                  <tr><th>#</th><th>رقم الامتحان</th><th>التاريخ</th><th>العلامة</th><th>الحالة</th></tr>
                </thead>
                <tbody>
                  ${examsView.length ? examsView.map((e, i) => `
                    <tr>
                      <td>${i + 1}</td>
                      <td>${escapeHtml(e.exam_id || "—")}</td>
                      <td>${escapeHtml(toYMDFromAny(e.created_at) || "—")}</td>
                      <td>${escapeHtml(e.obtained_marks ?? "—")}</td>
                      <td>${escapeHtml(Number(e.is_passed) === 1 ? "ناجح" : "راسب")}</td>
                    </tr>
                  `).join("") : `<tr><td colspan="5" style="text-align:center;color:#777">لا يوجد بيانات</td></tr>`}
                </tbody>
              </table>
            </div>

          </body>
        </html>
      `;
      openPrintWindow(html);
    } catch (e) {
      console.error(e);
      notify.error("فشلت الطباعة");
    }
  };

  // ✅ Print: التبويب الحالي فقط
  const handlePrintCurrentTab = () => {
    try {
      const baseStyle = `
        <style>
          body { font-family: Arial, sans-serif; padding: 16px; }
          h2 { margin: 0 0 10px; color: #6F013F; }
          .muted { color:#666; font-size:12px; margin-bottom:12px; }
          table { width:100%; border-collapse: collapse; margin-top: 10px; }
          th, td { border:1px solid #ccc; padding:8px; font-size:12px; text-align:right; }
          th { background:#f6f6f6; }
        </style>
      `;

      if (activeTab === "info") {
        const html = `
          <html dir="rtl">
            <head><meta charset="utf-8" />${baseStyle}</head>
            <body>
              <h2>معلومات الطالب</h2>
              <div class="muted">تاريخ الطباعة: ${escapeHtml(toYMD(new Date()))}</div>
              <table>
                <tbody>
                  <tr><th>الاسم</th><td>${escapeHtml(student?.full_name || "—")}</td></tr>
                  <tr><th>الهاتف</th><td>${escapeHtml(getPrimaryPhone(student))}</td></tr>
                  <tr><th>الجنس</th><td>${escapeHtml(student?.gender || "—")}</td></tr>
                  <tr><th>الفرع</th><td>${escapeHtml(student?.institute_branch?.name || "—")}</td></tr>
                  <tr><th>الشعبة</th><td>${escapeHtml(student?.batch?.name || "—")}</td></tr>
                  <tr><th>الحالة</th><td>${escapeHtml(student?.status?.name || "—")}</td></tr>
                  <tr><th>تاريخ التسجيل</th><td>${escapeHtml(student?.enrollment_date || "—")}</td></tr>
                  <tr><th>تاريخ بدء الأقساط</th><td>${escapeHtml(student?.enrollment_contract?.installments_start_date ? student.enrollment_contract.installments_start_date.split("T")[0] : "—")}</td></tr>
                </tbody>
              </table>
            </body>
          </html>
        `;
        openPrintWindow(html);
        return;
      }

      if (activeTab === "attendance") {
        const rows = attendanceSelectedIds.length > 0 
          ? attendanceView.filter(r => {
              const id = r.id || r.uuid || r.record_id || (r._unique_idx !== undefined ? `${r.date}-${r.check_in}-${r.check_out}-${r._unique_idx}` : `${r.date}-${r.check_in}-${r.check_out}`);
              return attendanceSelectedIds.includes(String(id));
            })
          : attendanceView;

        const html = `
          <html dir="rtl"><head><meta charset="utf-8" />${baseStyle}</head><body>
            <h2>الحضور والغياب</h2><div class="muted">${escapeHtml(attendanceLabel)}</div>
            <table>
              <thead><tr><th>#</th><th>التاريخ</th><th>الوصول</th><th>الانصراف</th><th>الحالة</th></tr></thead>
              <tbody>
                ${rows.map((r, i) => `
                  <tr>
                    <td>${i + 1}</td>
                    <td>${escapeHtml(r.date)}</td>
                    <td>${escapeHtml(r.check_in || "—")}</td>
                    <td>${escapeHtml(r.check_out || "—")}</td>
                    <td>${escapeHtml(r.status === "present" ? "حاضر" : r.status === "absent" ? "غائب" : r.status === "late" ? "متأخر" : r.status || "—")}</td>
                  </tr>
                `).join("")}
              </tbody>
            </table>
          </body></html>
        `;
        openPrintWindow(html);
        return;
      }

      if (activeTab === "payments") {
        const rows = paymentsSelectedIds.length > 0 
          ? paymentsView.filter(p => paymentsSelectedIds.includes(String(p.id)))
          : paymentsView;

        const html = `
          <html dir="rtl"><head><meta charset="utf-8" />${baseStyle}</head><body>
            <h2>الدفعات</h2><div class="muted">${escapeHtml(paymentsLabel)}</div>
            <table>
              <thead><tr><th>#</th><th>تاريخ الدفع</th><th>رقم الإيصال</th><th>المبلغ</th></tr></thead>
              <tbody>
                ${rows.map((p, i) => `
                  <tr>
                    <td>${i + 1}</td>
                    <td>${escapeHtml(toYMDFromAny(p.payment_date || p.date || p.paid_at || p.created_at || p.updated_at) || "—")}</td>
                    <td>${escapeHtml(p.receipt_number || "—")}</td>
                    <td>${escapeHtml(p.amount_usd ?? p.amount ?? "—")}</td>
                  </tr>
                `).join("")}
              </tbody>
            </table>
          </body></html>
        `;
        openPrintWindow(html);
        return;
      }

      if (activeTab === "examResults") {
        const rows = examsSelectedIds.length > 0 
          ? examsView.filter(e => examsSelectedIds.includes(String(e.id)))
          : examsView;

        const html = `
          <html dir="rtl"><head><meta charset="utf-8" />${baseStyle}</head><body>
            <h2>النتائج الامتحانية</h2><div class="muted">${escapeHtml(examsLabel)}</div>
            <table>
              <thead><tr><th>#</th><th>رقم الامتحان</th><th>التاريخ</th><th>العلامة</th><th>الحالة</th></tr></thead>
              <tbody>
                ${rows.map((e, i) => `
                  <tr>
                    <td>${i + 1}</td>
                    <td>${escapeHtml(e.exam_id || "—")}</td>
                    <td>${escapeHtml(toYMDFromAny(e.created_at) || "—")}</td>
                    <td>${escapeHtml(e.obtained_marks ?? "—")}</td>
                    <td>${escapeHtml(Number(e.is_passed) === 1 ? "ناجح" : "راسب")}</td>
                  </tr>
                `).join("")}
              </tbody>
            </table>
          </body></html>
        `;
        openPrintWindow(html);
        return;
      }
    } catch (e) {
      console.error(e);
      notify.error("فشلت الطباعة");
    }
  };

  /* ================= Actions ================= */

  const handleEditAttendanceClick = () => {
    if (activeTab !== "attendance") {
      notify.error("يرجى الانتقال إلى تبويب الغياب والحضور للتعديل");
      return;
    }
    setEditTrigger((v) => v + 1);
  };

  const openExportModal = (type) => {
    setExportType(type);
    setExportModalOpen(true);
  };

  const handleExportAll = () => {
    setExportLoading(true);
    try {
      if (exportType === "excel") handleExcelAll();
      else handlePrintAll();
    } finally {
      setExportLoading(false);
      setExportModalOpen(false);
    }
  };

  const handleExportCurrent = () => {
    setExportLoading(true);
    try {
      if (exportType === "excel") handleExcelCurrentTab();
      else handlePrintCurrentTab();
    } finally {
      setExportLoading(false);
      setExportModalOpen(false);
    }
  };

  return (
    <div dir="rtl" className="p-4 md:p-6 bg-[#FBFBFB] h-screen ">
      <div className="flex flex-col lg:flex-row-reverse gap-6">
        <div className="lg:w-1/4">
          <StudentCard
            student={student}
            selectedDate={selectedDate}
            onDateChange={setSelectedDate}
            activeTab={activeTab}
            attendanceRange={attendanceRange}
            paymentsRange={paymentsRange}
            examResultsRange={examResultsRange}
            onRangeChange={handleRangeChange}
            onEditAttendance={handleEditAttendanceClick}
          />
        </div>

        <div className="lg:w-3/4 flex flex-col gap-4">
          <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div className="flex gap-6 overflow-x-auto">
              <TabButton active={activeTab === "info"} onClick={() => setActiveTab("info")}>معلومات شخصية</TabButton>
              <TabButton active={activeTab === "attendance"} onClick={() => setActiveTab("attendance")}>الغياب والحضور</TabButton>
              <TabButton active={activeTab === "payments"} onClick={() => setActiveTab("payments")}>الدفعات</TabButton>
              <TabButton active={activeTab === "examResults"} onClick={() => setActiveTab("examResults")}>النتائج الامتحانية</TabButton>
            </div>
            <div className="flex gap-2 self-end md:self-auto">
              <ExcelButton onClick={() => openExportModal("excel")} />
              <PrintButton onClick={() => openExportModal("print")} />
            </div>
          </div>

          {activeTab === "info" && <StudentInfoTab student={student} />}

          {activeTab === "attendance" && (
            <AttendanceTab
              student={student}
              attendanceRange={attendanceRange}
              editTrigger={editTrigger}
              onEditRequest={(record) => {
                setRecordToEdit(record);
                setOpenEdit(true);
              }}
              {...activeTabProps}
            />
          )}

          {activeTab === "payments" && (
            <PaymentsTab 
              student={student} 
              paymentsRange={paymentsRange} 
              {...activeTabProps}
            />
          )}

          {activeTab === "examResults" && (
            <ExamResultsTab
              student={student}
              examResultsRange={examResultsRange}
              {...activeTabProps}
            />
          )}
        </div>
      </div>

      <EditAttendanceModal
        isOpen={openEdit}
        onClose={() => setOpenEdit(false)}
        record={recordToEdit}
        onSave={() => setOpenEdit(false)}
      />

      <ExportChoiceModal
        isOpen={exportModalOpen}
        onClose={() => setExportModalOpen(false)}
        type={exportType}
        loading={exportLoading}
        onAll={handleExportAll}
        onCurrent={handleExportCurrent}
      />
    </div>
  );
}


function TabButton({ active, children, onClick }) {
  return (
    <button
      onClick={onClick}
      className={`pb-2 border-b-2 transition text-sm ${
        active
          ? "border-black text-black"
          : "border-transparent text-gray-400 hover:text-gray-600"
      }`}
    >
      {children}
    </button>
  );
}
