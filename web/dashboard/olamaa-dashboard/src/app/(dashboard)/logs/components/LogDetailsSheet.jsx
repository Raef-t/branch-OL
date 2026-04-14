"use client";

import dayjs from "dayjs";
import { X } from "lucide-react";
import DiffRows from "./DiffRows";

const eventMeta = {
  created: {
    label: "إضافة",
    chip: "bg-blue-100 text-blue-700 border-blue-200",
  },
  updated: {
    label: "تعديل",
    chip: "bg-green-100 text-green-700 border-green-200",
  },
  deleted: {
    label: "حذف",
    chip: "bg-red-100 text-red-700 border-red-200",
  },
};

const InfoRow = ({ label, value }) => (
  <div className="text-sm">
    <div className="text-gray-500 text-[12px] mb-1">{label}</div>
    <div className="text-gray-800 font-semibold break-words">{value}</div>
  </div>
);

export default function LogDetailsSheet({ log, onClose }) {
  if (!log) return null;

  const meta = eventMeta[log.event] || eventMeta.updated;

  return (
    <div
      className="fixed inset-0 z-[100] bg-black/40 backdrop-blur-[2px] flex justify-start transition-all duration-300 animate-in fade-in"
      onClick={onClose}
    >
      <div
        className="w-full max-w-2xl h-full bg-white shadow-[-20px_0_50px_-12px_rgba(0,0,0,0.1)] border-r border-gray-100 overflow-y-auto 
                   animate-in slide-in-from-right duration-500 ease-out"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header Section with Gradient Backdrop */}
        <div className="relative overflow-hidden border-b border-gray-100 pb-6">
          <div className="absolute top-0 right-0 w-full h-24 bg-gradient-to-br from-[#6F013F]/5 to-transparent -z-10" />
          
          <div className="flex items-start justify-between gap-4 p-6 mt-2">
            <div className="space-y-3">
              <div className="flex items-center gap-3">
                <span className={`px-3 py-1 rounded-full text-[12px] font-bold border shadow-sm ${meta.chip}`}>
                  {meta.label}
                </span>
                <span className="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-md">
                  {dayjs(log.created_at).format("HH:mm  YYYY-MM-DD")}
                </span>
              </div>
              
              <div>
                <h2 className="text-2xl font-black text-gray-900 tracking-tight">
                  {log._modelName}
                </h2>
                <p className="text-sm text-gray-500 font-mono mt-1">
                  ID: <span className="text-[#D40078] font-bold">#{log.auditable_id}</span>
                </p>
              </div>

              <div className="flex items-center gap-2 text-sm text-gray-600">
                <div className="w-8 h-8 rounded-full bg-[#6F013F]/10 flex items-center justify-center text-[#6F013F] font-bold text-xs">
                  {(log.user_name || "??").substring(0, 2).toUpperCase()}
                </div>
                <span>بواسطة: <span className="font-bold text-gray-900">{log.user_name || "نظام آلي"}</span></span>
              </div>
            </div>

            <button
              className="p-2.5 rounded-xl hover:bg-gray-100/80 text-gray-400 hover:text-gray-900 transition-all border border-transparent hover:border-gray-200"
              onClick={onClose}
              aria-label="اغلاق"
            >
              <X size={22} strokeWidth={2.5} />
            </button>
          </div>
        </div>

        <div className="p-6 space-y-8">
          {/* Detailed Info Cards */}
          <div className="grid grid-cols-2 gap-4">
            <div className="bg-gray-50/50 p-4 rounded-2xl border border-gray-100 transition-hover hover:border-[#6F013F]/20">
              <InfoRow label="نوع الحدث" value={meta.label} />
            </div>
            <div className="bg-gray-50/50 p-4 rounded-2xl border border-gray-100 transition-hover hover:border-[#6F013F]/20">
              <InfoRow label="تاريخ التنفيذ" value={dayjs(log.created_at).format("YYYY-MM-DD")} />
            </div>
            <div className="col-span-2 bg-gray-50/50 p-4 rounded-2xl border border-gray-100 transition-hover hover:border-[#6F013F]/20">
              <InfoRow label="المسار البرمجي" value={log.auditable_type || "—"} />
            </div>
          </div>

          {/* Affected Fields Segment */}
          <div className="space-y-4">
            <div className="flex items-center gap-2">
              <div className="w-1.5 h-6 bg-[#6F013F] rounded-full" />
              <h3 className="text-lg font-bold text-gray-900">الحقول المتأثرة</h3>
              <span className="text-xs text-gray-400 font-medium">({log._changedCount || 0})</span>
            </div>

            <div className="p-5 rounded-3xl bg-[#FDF4F9] border border-[#6F013F]/5">
              {log._changedCount ? (
                <div className="flex flex-wrap gap-2.5">
                  {log._diffs?.map((d) => (
                    <span
                      key={d.key}
                      className="px-3 py-1.5 bg-white border border-[#6F013F]/10 rounded-xl text-[12px] font-bold text-[#6F013F] shadow-sm"
                    >
                      {d.key}
                    </span>
                  ))}
                </div>
              ) : (
                <div className="text-sm text-gray-500 italic">لا توجد تغييرات مسجلة في هذا الحدث</div>
              )}
            </div>
          </div>

          {/* Value Changes Section */}
          <div className="space-y-4 pb-10">
             <div className="flex items-center gap-2">
                <div className="w-1.5 h-6 bg-[#D40078] rounded-full" />
                <h3 className="text-lg font-bold text-gray-900">القيم (قبل / بعد)</h3>
            </div>
            
            <div className="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
              <DiffRows
                event={log.event}
                oldValues={log.old_values}
                newValues={log.new_values}
                compact={false}
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
