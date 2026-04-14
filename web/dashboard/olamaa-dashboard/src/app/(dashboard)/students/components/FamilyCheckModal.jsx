"use client";

import { X, User, Users, Phone, Hash } from "lucide-react";

export default function FamilyCheckModal({
  family,
  families = [],
  matchCount = 1,
  matchReason = "name",
  onConfirmAttach,
  onConfirmNew,
  onClose,
  loading = false,
}) {
  const isMultiple = matchCount > 1 || families.length > 1;
  const displayFamilies = isMultiple ? families : [family].filter(Boolean);

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-[9999] p-4 backdrop-blur-sm">
      <div className="bg-white w-full max-w-[500px] rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-200">
        {/* Header */}
        <div className="bg-[#6F013F] text-white px-6 py-4 flex justify-between items-center shrink-0">
          <div className="flex items-center gap-2">
            {isMultiple ? <Users className="w-5 h-5" /> : <User className="w-5 h-5" />}
            <h2 className="text-lg font-bold">عائلة موجودة مسبقاً</h2>
          </div>
          {onClose && (
            <button
              onClick={onClose}
              className="text-white/80 hover:text-white transition"
              disabled={loading}
            >
              <X className="w-6 h-6" />
            </button>
          )}
        </div>

        {/* Content */}
        <div className="p-6 overflow-y-auto flex-1">
          <div className="mb-4">
            <p className="text-gray-700 font-medium text-sm">
              {matchReason === "phone" ? (
                <span className="flex items-center gap-2 text-amber-600">
                  <Phone className="w-4 h-4" />
                  تم العثور على {isMultiple ? "عائلات" : "عائلة"} تملك نفس رقم الهاتف المسجل.
                </span>
              ) : (
                <span className="flex items-center gap-2 text-blue-600">
                  <Hash className="w-4 h-4" />
                  تم العثور على {isMultiple ? "عائلات مطابقة" : "عائلة مطابقة"} لبيانات الأب والأم.
                </span>
              )}
            </p>
            <p className="text-gray-500 text-xs mt-1">
              يرجى التأكد مما إذا كان الطالب ينتمي لإحدى العائلات أدناه لتجنب تكرار البيانات.
            </p>
          </div>

          <div className="space-y-3">
            {displayFamilies.map((f) => {
              const father = f.guardians?.find(g => g.relationship === "father");
              const mother = f.guardians?.find(g => g.relationship === "mother");
              const students = f.students || [];

              return (
                <div 
                  key={f.id} 
                  className="border border-gray-100 rounded-xl p-4 bg-gray-50 hover:border-[#6F013F]/30 transition group hover:shadow-md cursor-pointer"
                  onClick={() => onConfirmAttach(f.id)}
                >
                  <div className="flex justify-between items-start mb-2">
                    <span className="text-[10px] font-bold text-gray-400 bg-white px-2 py-0.5 rounded border border-gray-100">ID: {f.id}</span>
                    {f.match_reason === 'phone' && (
                       <span className="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-100">تطابق هاتفي</span>
                    )}
                  </div>
                  
                  <div className="space-y-1 mb-3">
                    <p className="text-sm">
                      <span className="text-gray-500 ml-1">الأب:</span> 
                      <span className="font-semibold text-gray-800">{father ? `${father.first_name} ${father.last_name}` : "—"}</span>
                    </p>
                    <p className="text-sm">
                      <span className="text-gray-500 ml-1">الأم:</span> 
                      <span className="font-semibold text-gray-800">{mother ? `${mother.first_name} ${mother.last_name}` : "—"}</span>
                    </p>
                  </div>

                  {students.length > 0 && (
                    <div className="bg-white/60 p-2 rounded-lg">
                      <p className="text-[10px] font-bold text-gray-400 uppercase mb-1">الطلاب المسجلون سابقاً:</p>
                      <div className="flex flex-wrap gap-1">
                        {students.slice(0, 3).map(s => (
                          <span key={s.id} className="text-[11px] bg-white border border-gray-100 px-2 py-0.5 rounded-full text-gray-600">
                            {s.first_name} {s.last_name}
                          </span>
                        ))}
                        {students.length > 3 && <span className="text-[11px] text-gray-400">+{students.length - 3} آخرون</span>}
                      </div>
                    </div>
                  )}

                  <div className="mt-3 flex justify-end">
                    <span className="text-xs font-bold text-[#6F013F] opacity-0 group-hover:opacity-100 transition flex items-center gap-1">
                      ربط بهذه العائلة ←
                    </span>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Footer */}
        <div className="p-6 border-t border-gray-100 bg-gray-50 shrink-0">
          <button
            onClick={onConfirmNew}
            disabled={loading}
            className="w-full py-3 rounded-xl border border-gray-200 bg-white text-gray-600 font-bold hover:bg-gray-100 transition shadow-sm mb-3"
          >
            لا، هذه عائلة جديدة كلياً
          </button>
          
          {loading && (
            <p className="text-center text-xs text-gray-500 animate-pulse">جارٍ معالجة طلبك...</p>
          )}
        </div>
      </div>
    </div>
  );
}
