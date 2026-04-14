"use client";
import { useState } from "react";
import api from "@/lib/config/axiosConfig";
import { toast } from "react-hot-toast";
import { Copy, Check, Eye, EyeOff } from "lucide-react";

export default function CredentialsModal({ employee, onClose }) {
  const [newPassword, setNewPassword] = useState("");
  const [isUpdating, setIsUpdating] = useState(false);
  const [currentPassword, setCurrentPassword] = useState(employee?.password || "********");
  const [copiedField, setCopiedField] = useState(null);
  const [showCurrent, setShowCurrent] = useState(true);

  if (!employee) return null;

  const handleCopy = (text, field) => {
    navigator.clipboard.writeText(text);
    setCopiedField(field);
    toast.success("تم النسخ للحافظة");
    setTimeout(() => setCopiedField(null), 2000);
  };

  const handleUpdatePassword = async () => {
    if (!newPassword || newPassword.length < 8) {
      toast.error("كلمة المرور يجب أن تكون 8 أحرف على الأقل");
      return;
    }

    setIsUpdating(true);
    try {
      await api.put(`/users/${employee.userId}`, { 
        password: newPassword,
        force_password_change: true 
      });
      toast.success("تم تحديث كلمة المرور بنجاح");
      setCurrentPassword(newPassword);
      setNewPassword("");
      setShowCurrent(true);
    } catch (error) {
      console.error("Password Update Error:", error);
      toast.error(error.response?.data?.message || "فشل تحديث كلمة المرور");
    } finally {
      setIsUpdating(false);
    }
  };

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
      onClick={onClose}
    >
      <div
        dir="rtl"
        className="w-full max-w-[420px] rounded-3xl bg-white shadow-2xl overflow-hidden border border-gray-100"
        onClick={(e) => e.stopPropagation()}
      >
        {/* الرأس */}
        <div className="bg-[#6F013F] p-6 text-white text-center">
          <h3 className="text-xl font-bold">بيانات الدخول</h3>
          <p className="text-[11px] opacity-80 mt-1 font-medium italic">
            للموظف: {employee.name}
          </p>
        </div>

        <div className="p-8 space-y-8">
          {/* عرض البيانات الحالية */}
          <div className="space-y-4">
            <label className="text-xs font-bold text-gray-400 uppercase tracking-widest block pr-1">
              البيانات الحالية
            </label>
            <div className="space-y-3">
              {/* اسم المستخدم */}
              <div className="group flex items-center justify-between p-3.5 rounded-2xl bg-gray-50 border border-gray-100 transition-all hover:bg-white hover:border-[#6F013F]/30 hover:shadow-sm">
                <div className="flex flex-col">
                  <span className="text-[10px] text-gray-400 font-bold uppercase mb-0.5">اسم المستخدم</span>
                  <span className="font-mono text-sm font-bold text-gray-800 tracking-wider">
                    {employee.username}
                  </span>
                </div>
                <button 
                  onClick={() => handleCopy(employee.username, 'user')}
                  className="p-2 rounded-xl text-gray-400 hover:text-[#B00069] hover:bg-white transition-all"
                >
                  {copiedField === 'user' ? <Check size={16} /> : <Copy size={16} />}
                </button>
              </div>

              {/* كلمة المرور */}
              <div className="group flex items-center justify-between p-3.5 rounded-2xl bg-gray-50 border border-gray-100 transition-all hover:bg-white hover:border-[#6F013F]/30 hover:shadow-sm">
                <div className="flex flex-col">
                  <span className="text-[10px] text-gray-400 font-bold uppercase mb-0.5">كلمة المرور</span>
                  <span className={`font-mono text-sm font-bold tracking-widest ${showCurrent ? 'text-[#B00069]' : 'text-gray-300'}`}>
                    {showCurrent ? currentPassword : '••••••••••••'}
                  </span>
                </div>
                <div className="flex items-center gap-1">
                  <button 
                    onClick={() => setShowCurrent(!showCurrent)}
                    className="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-white transition-all"
                  >
                    {showCurrent ? <EyeOff size={16} /> : <Eye size={16} />}
                  </button>
                  <button 
                    onClick={() => handleCopy(currentPassword, 'pass')}
                    disabled={currentPassword.includes('***')}
                    className="p-2 rounded-xl text-gray-400 hover:text-[#B00069] hover:bg-white transition-all disabled:opacity-30"
                  >
                    {copiedField === 'pass' ? <Check size={16} /> : <Copy size={16} />}
                  </button>
                </div>
              </div>
            </div>
            {currentPassword.includes('***') && (
              <p className="text-[10px] text-orange-500 font-medium leading-relaxed pr-1 italic">
                * كلمات المرور القديمة مشفرة ولا يمكن رؤيتها. قم بتعيين كلمة جديدة إذا لزم الأمر.
              </p>
            )}
          </div>

          {/* تعيين كلمة مرور جديدة */}
          <div className="space-y-4 pt-4 border-t border-gray-50">
            <label className="text-xs font-bold text-gray-400 uppercase tracking-widest block pr-1">
              تغيير كلمة المرور
            </label>
            <div className="flex gap-2">
              <input
                type="text"
                value={newPassword}
                onChange={(e) => setNewPassword(e.target.value)}
                placeholder="أدخل كلمة مرور جديدة..."
                className="flex-1 h-12 px-4 rounded-2xl border border-gray-100 bg-gray-50 text-sm font-bold text-[#6F013F] focus:bg-white focus:border-[#B00069] outline-none transition-all placeholder:font-normal placeholder:text-gray-300"
              />
              <button
                onClick={handleUpdatePassword}
                disabled={isUpdating}
                className={`h-12 px-6 rounded-2xl bg-[#6F013F] text-white text-sm font-bold shadow-lg shadow-[#6F013F]/20 transition-all ${
                  isUpdating ? "opacity-50 cursor-not-allowed" : "hover:bg-[#52012f] active:scale-95"
                }`}
              >
                {isUpdating ? "جاري..." : "تحديث"}
              </button>
            </div>
          </div>

          <button
            type="button"
            onClick={onClose}
            className="w-full h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-gray-500 text-sm font-bold hover:bg-gray-100 hover:text-gray-700 transition-all active:scale-[0.98]"
          >
            إغلاق النافذة
          </button>
        </div>
      </div>
    </div>
  );
}