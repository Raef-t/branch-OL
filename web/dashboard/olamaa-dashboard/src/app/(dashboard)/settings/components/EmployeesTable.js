"use client";

import { useMemo, useState, useEffect } from "react";
import DataTable from "@/components/common/DataTable";
import { ChevronDown } from "lucide-react";

export default function EmployeesTable({
  employees = [],
  isLoading = false,
  selectedIds = [],
  onSelectChange,
  onToggleAccount,
  onToggleApproval,
  onCreateAccount,
  onViewCredentials,
  onAssignRole,
  onRemoveRole,
}) {
  const roleMap = {
    admin: "مدير النظام",
    manager: "مدير",
    teacher: "مدرس",
    employee_accountant: "محاسب",
    employee_data_entry: "مدخل بيانات",
    employee_auditor: "مدقق",
    student: "طالب",
    parent: "ولي أمر"
  };
  const [openDropdownId, setOpenDropdownId] = useState(null);

  useEffect(() => {
    const handleClickOutside = () => {
      setOpenDropdownId(null);
    };

    window.addEventListener("click", handleClickOutside);
    return () => window.removeEventListener("click", handleClickOutside);
  }, []);

  const columns = useMemo(
    () => [
      {
        header: "الاسم",
        key: "name",
        render: (val, row) => (
          <div className="flex items-center gap-3">
            <img
              src={row.avatar}
              alt={val}
              className="w-10 h-10 rounded-full object-cover"
            />
            <div className="flex flex-col text-right">
              <span className="font-medium text-gray-700">{val}</span>
              <span className="text-xs text-gray-400">{row.email}</span>
            </div>
          </div>
        ),
      },
      {
        header: "الدور",
        key: "roles",
        render: (_, row) => (
          <div className="flex flex-wrap gap-2">
            {row.roles?.length > 0 ? (
              row.roles.map((role) => (
                <span
                  key={role}
                  className="px-3 py-1 text-xs rounded-xl bg-purple-100 text-purple-700 font-medium flex items-center gap-2"
                >
                  {roleMap[role] || role}
                  <button
                    type="button"
                    onClick={(e) => {
                      e.stopPropagation();
                      onRemoveRole?.(row.id, row.userId, role);
                    }}
                    className="opacity-60 hover:opacity-100 text-red-500 transition text-sm font-bold leading-none cursor-pointer"
                    title="حذف الدور"
                  >
                    ×
                  </button>
                </span>
              ))
            ) : (
              <span className="text-gray-400 text-xs">لا يوجد دور</span>
            )}
          </div>
        ),
      },
      {
        header: "تنشيط (موظف / حساب)",
        key: "isActive",
        className: "w-1 whitespace-nowrap",
        render: (val, row) => (
          <div className="flex flex-col gap-2 py-1">
            {/* مفتاح تنشيط الموظف */}
            <div
              className="flex items-center gap-8 justify-start"
              title="يتحكم في ظهور الموظف في القوائم والتعيينات (مدرس، مشرف.. إلخ)"
            >
              <span className="text-[10px] text-gray-400 font-medium min-w-[35px]">الموظف</span>
              <AccountSwitch
                checked={val}
                onChange={() => onToggleAccount?.(row.id, val)}
              />
            </div>

            {/* مفتاح تنشيط الحساب (is_approved) */}
            <div
              className="flex items-center gap-8 justify-start border-t border-gray-50 pt-1.5"
              title="يتحكم في قدرة الموظف على تسجيل الدخول إلى حسابه في النظام"
            >
              <span className="text-[10px] text-gray-400 font-medium min-w-[35px]">الحساب</span>
              {row.accountType === "toggle" ? (
                <AccountSwitch
                  checked={row.isApproved}
                  onChange={() =>
                    onToggleApproval?.(row.id, row.userId, row.isApproved)
                  }
                  activeColor="#D40078"
                />
              ) : (
                <button
                  type="button"
                  onClick={() => onCreateAccount?.(row)}
                  className="text-xs text-[#6F013F] font-bold hover:underline"
                  title="إنشاء حساب مستخدم"
                >
                  + إنشاء
                </button>
              )}
            </div>
          </div>
        ),
      },
      {
        header: "كلمة السر",
        key: "id",
        render: (_, row) => (
          <button
            type="button"
            onClick={() => onViewCredentials?.(row)}
            className="flex items-center gap-3 text-gray-700 hover:opacity-70"
          >
            <span className="tracking-[0.3em]">*******</span>
            <EyeSlashIcon />
          </button>
        ),
      },
    ],
    [onToggleAccount, onToggleApproval, onCreateAccount, onViewCredentials, onRemoveRole],
  );

  const renderActions = (employee) => (
    <div
      className="relative inline-block text-right"
      onClick={(e) => e.stopPropagation()}
    >
      <button
        type="button"
        onClick={(e) => {
          e.stopPropagation();
          setOpenDropdownId((prev) => (prev === employee.id ? null : employee.id));
        }}
        className="inline-flex items-center gap-2 rounded-lg bg-[#F4E8FA] px-4 py-2 text-xs font-medium text-[#6F013F] hover:opacity-90"
      >
        Edit Role
        <ChevronDown size={14} />
      </button>

      {/* Dropdown */}
      {openDropdownId === employee.id && (
        <div className="absolute left-0 mt-2 w-[160px] bg-[#F4E8FA] rounded-xl shadow-lg border border-[#EADCF3] overflow-hidden z-50">
          {Object.entries(roleMap).map(([roleKey, roleLabel]) => (
            <button
              key={roleKey}
              onClick={() => {
                onAssignRole?.(employee.id, employee.userId, roleKey);
                setOpenDropdownId(null);
              }}
              className="w-full text-right px-4 py-2 text-sm text-[#6F013F] hover:bg-[#EADCF3] transition"
            >
              {roleLabel}
            </button>
          ))}
        </div>
      )}
    </div>
  );

  return (
    <DataTable
      data={employees}
      columns={columns}
      isLoading={isLoading}
      selectedIds={selectedIds}
      onSelectChange={onSelectChange}
      renderActions={renderActions}
      pageSize={6}
      emptyMessage="لا توجد بيانات."
    />
  );
}

function AccountSwitch({ checked, onChange, activeColor = "#6F013F" }) {
  return (
    <button
      type="button"
      onClick={onChange}
      className={`relative w-9 h-5 rounded-full transition ${checked ? "" : "bg-gray-200"
        }`}
      style={checked ? { backgroundColor: activeColor } : {}}
    >
      <span
        className={`absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-all ${checked ? "right-0.5" : "left-0.5"
          }`}
      />
    </button>
  );
}

function EyeSlashIcon() {
  return (
    <svg
      width="18"
      height="18"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="1.8"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M10.58 10.58a2 2 0 1 0 2.83 2.83" />
      <path d="M9.88 5.09A10.94 10.94 0 0 1 12 4.91c5 0 9.27 3.11 11 7.5a10.49 10.49 0 0 1-4.05 5.07" />
      <path d="M6.61 6.61A10.98 10.98 0 0 0 1 12.41c1.73 4.39 6 7.5 11 7.5 1.75 0 3.41-.38 4.9-1.05" />
      <path d="M3 3l18 18" />
    </svg>
  );
}

