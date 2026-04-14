"use client";

import { useMemo } from "react";
import { Eye, EyeOff, Archive, ArchiveRestore, CheckCircle2, XCircle, Pencil, Trash2 } from "lucide-react";
import DataTable from "@/components/common/DataTable";

/* ================= Helpers ================= */

const getGenderBadge = (gender) => {
  switch (gender) {
    case "male":
      return {
        text: "ذكور",
        className: "bg-blue-100 text-blue-700",
      };
    case "female":
      return {
        text: "إناث",
        className: "bg-pink-100 text-pink-700",
      };
    case "mixed":
      return {
        text: "مختلطة",
        className: "bg-purple-100 text-purple-700",
      };
    default:
      return {
        text: "غير محدد",
        className: "bg-gray-100 text-gray-600",
      };
  }
};

function StatusBadge({ text, color }) {
  const colors = {
    green: "bg-green-100 text-green-700",
    orange: "bg-orange-100 text-orange-700",
    gray: "bg-gray-200 text-gray-700",
    blue: "bg-blue-100 text-blue-700",
  };

  return (
    <span
      className={`px-3 py-1 text-xs rounded-xl whitespace-nowrap ${colors[color]}`}
    >
      {text}
    </span>
  );
}

export default function BatchesTable({
  batches = [],
  isLoading = false,
  selectedIds = [],
  onSelectChange,
  onEdit,
  onDelete,
  onToggleStatus,
  activeView = "all",
}) {
  const columns = useMemo(
    () => [
      { header: "#", key: "id" },
      { header: "اسم الشعبة", key: "name" },
      {
        header: "الموظف",
        key: "batch_employees",
        render: (val) => {
          if (!val || val.length === 0) return "—";
          return val.map((item) => item.employee?.full_name).join("، ") || "—";
        },
      },
      {
        header: "الصف",
        key: "academic_branch",
        sortKey: "academic_branch.name",
        render: (val) => val?.name || "—",
      },
      {
        header: "الفرع",
        key: "institute_branch",
        sortKey: "institute_branch.name",
        render: (val) => val?.name || "—",
      },
      { header: "تاريخ الدورة", key: "start_date" },
      {
        header: "نوع الدورة",
        key: "gender_type",
        render: (val) => {
          const gender = getGenderBadge(val);
          return (
            <span
              className={`px-3 py-1 text-xs rounded-xl ${gender.className}`}
            >
              {gender.text}
            </span>
          );
        },
      },
      {
        header: "ملاحظة",
        key: "note",
        sortable: false,
        render: (val) => (
          <span className="text-xs text-gray-600 line-clamp-2 max-w-[220px] inline-block">
            {val || "—"}
          </span>
        ),
      },
      {
        header: "الحالة",
        key: "status",
        sortable: false,
        render: (_, batch) => {
          if (batch.is_completed)
            return <StatusBadge text="مكتملة" color="green" />;
          if (batch.is_hidden)
            return <StatusBadge text="مخفية" color="orange" />;
          if (batch.is_archived)
            return <StatusBadge text="مؤرشفة" color="gray" />;
          return <StatusBadge text="نشطة" color="blue" />;
        },
      },
    ],
    [],
  );

  const renderActions = (row) => (
    <div className="flex justify-center gap-2">
      {/* Toggle Hide */}
      <button
        onClick={() => onToggleStatus?.(row.id, "is_hidden")}
        className="hover:opacity-70 transition p-1 rounded-md hover:bg-gray-100"
        title={row.is_hidden ? "إظهار الشعبة" : "إخفاء الشعبة"}
      >
        {row.is_hidden ? (
          <Eye size={16} className="text-green-600" />
        ) : (
          <EyeOff size={16} className="text-orange-500" />
        )}
      </button>

      {/* Toggle Archive */}
      <button
        onClick={() => onToggleStatus?.(row.id, "is_archived")}
        className="hover:opacity-70 transition p-1 rounded-md hover:bg-gray-100"
        title={row.is_archived ? "إلغاء الأرشفة" : "أرشفة الشعبة"}
      >
        {row.is_archived ? (
          <ArchiveRestore size={16} className="text-blue-600" />
        ) : (
          <Archive size={16} className="text-gray-500" />
        )}
      </button>

      {/* Toggle Complete */}
      <button
        onClick={() => onToggleStatus?.(row.id, "is_completed")}
        className="hover:opacity-70 transition p-1 rounded-md hover:bg-gray-100"
        title={row.is_completed ? "إلغاء الاكتمال" : "تحديد كمكتملة"}
      >
        {row.is_completed ? (
          <XCircle size={16} className="text-red-400" />
        ) : (
          <CheckCircle2 size={16} className="text-green-500" />
        )}
      </button>

      {/* Edit */}
      <button
        onClick={() => onEdit?.(row.id)}
        className="hover:opacity-70 transition p-1 rounded-md hover:bg-gray-100"
        title="تعديل الشعبة"
      >
        <Pencil size={16} className="text-blue-500" />
      </button>

      {/* Delete */}
      <button
        onClick={() => onDelete?.(row)}
        className="hover:opacity-70 transition p-1 rounded-md hover:bg-gray-100"
        title="حذف الشعبة"
      >
        <Trash2 size={16} className="text-red-500" />
      </button>
    </div>
  );

  return (
    <div className="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
      <DataTable
        data={batches}
        columns={columns}
        isLoading={isLoading}
        selectedIds={selectedIds}
        onSelectChange={onSelectChange}
        renderActions={renderActions}
        serverSide={false}
        emptyMessage="لا توجد شعب حالياً."
      />
    </div>
  );
}
