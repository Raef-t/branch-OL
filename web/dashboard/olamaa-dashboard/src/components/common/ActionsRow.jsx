"use client";

import DashboardButton from "./DashboardButton";

export default function ActionsRow({
  onAdd,
  onView,

  showSelectAll = false,
  isAllSelected = false,
  onToggleSelectAll,

  showViewAll = false,
  onViewAll,
  viewAllLabel = "عرض كل البيانات",

  extraButtons = [],

  addLabel = "إضافة عنصر",
  viewLabel = "عرض البيانات",
  selectAllLabel = "تحديد الكل",
  className = "",
  children,

  // ✅ Report specific checkboxes
  showCoverCheckbox = false,
  coverLabel = "مع غلاف",
  onCoverChange,
  isCoverChecked,

  showAttendanceCheckbox = false,
  attendanceLabel = "مع حضور",
  onAttendanceChange,
  isAttendanceChecked,
}) {
  return (
    <div className={`flex flex-wrap gap-2 items-center ${className}`}>
      {!!addLabel && (
        <DashboardButton
          label={addLabel}
          icon={<span className="text-md leading-none">+</span>}
          color="pink"
          onClick={onAdd}
        />
      )}

      {!!viewLabel && (
        <DashboardButton
          label={viewLabel}
          icon={<i className="text-sm not-italic">≡</i>}
          color="green"
          onClick={onView}
        />
      )}

      {showViewAll && (
        <DashboardButton
          label={viewAllLabel}
          icon={<span className="text-sm">↺</span>}
          color="green"
          onClick={onViewAll}
        />
      )}

      {showSelectAll && (
        <DashboardButton
          label={selectAllLabel}
          icon={
            <input
              type="checkbox"
              checked={isAllSelected}
              readOnly
              className="accent-[#6F013F]"
            />
          }
          color="gray"
          onClick={onToggleSelectAll}
        />
      )}

      {showCoverCheckbox && (
        <DashboardButton
          label={coverLabel}
          color="white"
          className="!bg-[#DEFFE0] !text-gray-700 hover:bg-gray-100 border-0"
          icon={
            <input
              type="checkbox"
              className="accent-[#6F013F] cursor-pointer"
              checked={isCoverChecked}
              onChange={(e) => onCoverChange?.(e.target.checked)}
              onClick={(e) => e.stopPropagation()}
            />
          }
          onClick={() => onCoverChange?.(!isCoverChecked)}
        />
      )}

      {showAttendanceCheckbox && (
        <DashboardButton
          label={attendanceLabel}
          color="white"
          className="!bg-[#FFE5F4] !text-gray-700 hover:opacity-90 border-0"
          icon={
            <input
              type="checkbox"
              className="accent-[#6F013F] cursor-pointer"
              checked={isAttendanceChecked}
              onChange={(e) => onAttendanceChange?.(e.target.checked)}
              onClick={(e) => e.stopPropagation()}
            />
          }
          onClick={() => onAttendanceChange?.(!isAttendanceChecked)}
        />
      )}

      {Array.isArray(extraButtons) &&
        extraButtons.map((btn, idx) => (
          <DashboardButton
            key={btn.key ?? idx}
            label={btn.label}
            icon={btn.icon}
            color={btn.color ?? "green"}
            onClick={btn.onClick}
            className={btn.className}
          />
        ))}

      {children}
    </div>
  );
}
