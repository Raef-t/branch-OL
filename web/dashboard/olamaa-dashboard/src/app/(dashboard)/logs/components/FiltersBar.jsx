import SearchableSelect from "@/components/common/SearchableSelect";
import { RotateCcw } from "lucide-react";

export default function FiltersBar({
  eventValue,
  onEventChange,
  eventOptions,
  userValue,
  onUserChange,
  userOptions,
  onReset,
}) {
  const hasFilters = eventValue || userValue;

  return (
    <div className="flex items-end gap-3 flex-wrap p-1">
      <div className="flex-1 min-w-[200px]">
        <SearchableSelect
          label="نوع العملية"
          value={eventValue}
          onChange={onEventChange}
          options={eventOptions}
          placeholder="كل العمليات..."
          allowClear
        />
      </div>

      <div className="flex-1 min-w-[200px]">
        <SearchableSelect
          label="المستخدم"
          value={userValue}
          onChange={onUserChange}
          options={userOptions}
          placeholder="كل المستخدمين..."
          allowClear
        />
      </div>

      {hasFilters && (
        <button
          onClick={onReset}
          className="p-2.5 rounded-xl border border-red-100 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-all flex items-center gap-2 text-sm font-bold shadow-sm mb-[2px]"
          title="مسح كافة الفلاتر"
        >
          <RotateCcw size={18} />
          <span className="hidden lg:inline">إعادة ضبط</span>
        </button>
      )}
    </div>
  );
}
