"use client";

import { X } from "lucide-react";
import { useLazyGetFamilyQuery, useResetPasswordMutation, useActivateFamilyUserMutation } from "@/store/services/familiesApi";
import { useEffect, useState } from "react";
import toast from "react-hot-toast";

const relationshipLabel = (r) => {
    const map = {
        father: "أب",
        mother: "أم",
        legal_guardian: "ولي أمر قانوني",
        other: "أخرى",
    };
    return map[r] || r || "—";
};

export default function FamilyDetailsModal({ open, onClose, familyId }) {
    const [fetchFamily, { data: familyRes, isFetching }] =
        useLazyGetFamilyQuery();
    const [resetPassword, { isLoading: isResetting }] = useResetPasswordMutation();
    const [activateFamilyUser, { isLoading: isActivating }] = useActivateFamilyUserMutation();
    const [family, setFamily] = useState(null);

    const handleCreateStudentAccount = async (studentId, studentName) => {
        try {
            // سنستخدم نفس إندبوينت تفعيل العائلة لأنه الآن أصبح يُنشئ حسابات للطلاب أيضاً
            // أو يمكننا استدعاء إندبوينت عام إذا توفر، لكن الأسهل الآن هو إعادة تفعيل العائلة
            await activateFamilyUser(family.id).unwrap();
            toast.success(`تم إنشاء حساب للطالب ${studentName} وتحديث حالة العائلة`);
            fetchFamily(family.id); // إعادة جلب البيانات لتحديث العرض
        } catch (err) {
            toast.error(err?.data?.message || "فشل إنشاء الحساب");
        }
    };

    const handleResetPassword = async (userId, name) => {
        if (!confirm(`هل أنت متأكد من إعادة تعيين كلمة المرور للمستخدم (${name})؟ ستعود إلى القيمة الافتراضية 12345678 وسيتم إجباره على تغييرها عند الدخول.`)) return;
        
        try {
            await resetPassword(userId).unwrap();
            toast.success("تم إعادة تعيين كلمة المرور بنجاح");
        } catch (err) {
            toast.error(err?.data?.message || "فشل إعادة تعيين كلمة المرور");
        }
    };

    useEffect(() => {
        if (open && familyId) {
            fetchFamily(familyId)
                .unwrap()
                .then((res) => {
                    setFamily(res?.data ?? res);
                })
                .catch(() => { });
        }
    }, [open, familyId, fetchFamily]);

    useEffect(() => {
        if (!open) setFamily(null);
    }, [open]);

    if (!open) return null;

    const students = family?.students ?? [];
    const guardians = family?.guardians ?? [];

    return (
        <div className="fixed inset-0 bg-black/40 z-50 backdrop-blur-md flex justify-start">
            <div className="w-[500px] bg-white h-full shadow-xl overflow-y-auto">
                {/* HEADER */}
                <div className="sticky top-0 bg-white z-10 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 className="text-[#6F013F] font-semibold text-lg">
                        تفاصيل العائلة #{familyId}
                    </h2>
                    <button onClick={onClose}>
                        <X className="w-5 h-5 text-gray-500 hover:text-gray-700" />
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {isFetching ? (
                        <div className="animate-pulse space-y-4">
                            {[...Array(4)].map((_, i) => (
                                <div key={i} className="h-8 bg-gray-100 rounded-lg" />
                            ))}
                        </div>
                    ) : !family ? (
                        <div className="py-10 text-center text-gray-400">
                            لم يتم العثور على بيانات
                        </div>
                    ) : (
                        <>
                            {/* FAMILY INFO */}
                            <div className="bg-gradient-to-l from-[#fbeaf3] to-white rounded-xl p-5 border border-pink-100">
                                <h3 className="text-sm font-semibold text-[#6F013F] mb-3">
                                    معلومات العائلة
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mt-3">
                                    <div className="flex flex-col sm:flex-row sm:items-center gap-1">
                                        <span className="text-gray-500 whitespace-nowrap">رقم العائلة:</span>
                                        <span className="font-medium">#{family.id}</span>
                                    </div>
                                    <div className="flex flex-col sm:flex-row sm:items-center gap-1">
                                        <span className="text-gray-500 whitespace-nowrap">حالة الحساب:</span>
                                        <span className="mr-2">
                                            {family.user_id ? (
                                                <span className="text-green-600 font-medium whitespace-nowrap">
                                                    مفعّل
                                                </span>
                                            ) : (
                                                <span className="text-amber-600 font-medium whitespace-nowrap">
                                                    غير مفعّل
                                                </span>
                                            )}
                                        </span>
                                    </div>
                                    {family.user && (
                                        <>
                                            <div className="flex flex-col sm:flex-row sm:items-center gap-1">
                                                <span className="text-gray-500 whitespace-nowrap">اسم المستخدم:</span>
                                                <span className="font-bold text-[#6F013F] whitespace-nowrap break-words px-2 py-0.5 bg-pink-50 rounded border border-pink-100">
                                                    {family.user.unique_id || family.user.name}
                                                </span>
                                            </div>
                                            <div className="flex flex-col sm:flex-row sm:items-center gap-1 justify-between w-full md:col-span-2 pt-2 border-t border-pink-100 mt-2">
                                                <div className="flex items-center gap-2">
                                                    <span className="text-gray-500 whitespace-nowrap">الدور:</span>
                                                    <span className="whitespace-nowrap font-medium">{family.user.role}</span>
                                                </div>
                                                <button 
                                                    onClick={() => handleResetPassword(family.user.id, family.user.name)}
                                                    className="text-xs text-white bg-[#6F013F] hover:bg-[#AD164C] px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 shadow-sm"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" className="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M3 21v-5h5"/></svg>
                                                    ترسيت الباسورد
                                                </button>
                                            </div>
                                        </>
                                    )}
                                    <div className="flex flex-col lg:flex-row lg:items-center md:col-span-2 gap-1">
                                        <span className="text-gray-500 whitespace-nowrap">تاريخ الإنشاء:</span>
                                        <span className="font-medium text-gray-800" dir="ltr">
                                            {family.created_at?.split(" ")[0] ?? "—"}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {/* STUDENTS */}
                            <div>
                                <div className="flex items-center gap-2 mb-3">
                                    <div className="w-1 h-5 bg-blue-500 rounded-full" />
                                    <h3 className="text-sm font-semibold text-gray-700">
                                        الطلاب التابعون ({students.length})
                                    </h3>
                                </div>

                                {students.length === 0 ? (
                                    <div className="bg-gray-50 rounded-xl p-4 text-center text-sm text-gray-400">
                                        لا يوجد طلاب مرتبطون بهذه العائلة
                                    </div>
                                ) : (
                                    <div className="space-y-2">
                                        {students.map((s) => (
                                            <div
                                                key={s.id}
                                                className="flex items-center justify-between bg-blue-50 rounded-xl px-4 py-3 border border-blue-100"
                                            >
                                                <div className="flex-1">
                                                    <div className="flex items-center justify-between">
                                                        <span className="text-sm font-semibold text-gray-800">
                                                            {s.full_name}
                                                        </span>
                                                        <div className="flex items-center gap-2">
                                                            {!s.user ? (
                                                                <span className="text-[10px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">لا يوجد حساب</span>
                                                            ) : s.user.is_approved ? (
                                                                <span className="text-[10px] text-green-600 bg-green-100 px-2 py-0.5 rounded-full border border-green-200">مفعّل</span>
                                                            ) : (
                                                                <span className="text-[10px] text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-200">غير مفعّل</span>
                                                            )}
                                                            <span className="text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">طالب</span>
                                                        </div>
                                                    </div>
                                                    <div className="flex items-center justify-between mt-2">
                                                        <div className="flex items-center gap-2">
                                                            <span className="text-xs text-gray-400">Username:</span>
                                                            <span className="text-xs font-bold text-blue-700 bg-white px-2 py-0.5 rounded border border-blue-200">
                                                                {s.user?.unique_id || "—"}
                                                            </span>
                                                        </div>
                                                        {s.user ? (
                                                            <button 
                                                                onClick={() => handleResetPassword(s.user.id, s.full_name)}
                                                                className="text-xs text-blue-700 bg-blue-100 hover:bg-blue-200 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 border border-blue-200"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" className="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M3 21v-5h5"/></svg>
                                                                إعادة تعيين كلمة المرور
                                                            </button>
                                                        ) : (
                                                            <button 
                                                                onClick={() => handleCreateStudentAccount(s.id, s.full_name)}
                                                                className="text-xs text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 shadow-sm"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" className="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                                                إنشاء حساب طالب
                                                            </button>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>

                            {/* GUARDIANS */}
                            <div>
                                <div className="flex items-center gap-2 mb-3">
                                    <div className="w-1 h-5 bg-purple-500 rounded-full" />
                                    <h3 className="text-sm font-semibold text-gray-700">
                                        أولياء الأمور ({guardians.length})
                                    </h3>
                                </div>

                                {guardians.length === 0 ? (
                                    <div className="bg-gray-50 rounded-xl p-4 text-center text-sm text-gray-400">
                                        لا يوجد أولياء أمور مرتبطون بهذه العائلة
                                    </div>
                                ) : (
                                    <div className="space-y-2">
                                        {guardians.map((g) => (
                                            <div
                                                key={g.id}
                                                className="bg-purple-50 rounded-xl px-4 py-3 border border-purple-100"
                                            >
                                                <div className="flex items-center justify-between">
                                                    <div>
                                                        <span className="text-sm font-medium text-gray-800">
                                                            {g.first_name ?? ""} {g.last_name ?? ""}
                                                        </span>
                                                        <span className="text-xs text-gray-400 mr-2">
                                                            #{g.id}
                                                        </span>
                                                    </div>
                                                    <div className="flex items-center gap-2">
                                                        {g.is_primary_contact && (
                                                            <span className="text-xs text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">
                                                                جهة اتصال رئيسية
                                                            </span>
                                                        )}
                                                        <span className="text-xs text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">
                                                            {relationshipLabel(g.relationship)}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="grid grid-cols-2 gap-2 mt-2 text-xs text-gray-500">
                                                    {g.phone && (
                                                        <div>
                                                            الهاتف:{" "}
                                                            <span className="text-gray-700">{g.phone}</span>
                                                        </div>
                                                    )}
                                                    {g.occupation && (
                                                        <div>
                                                            المهنة:{" "}
                                                            <span className="text-gray-700">
                                                                {g.occupation}
                                                            </span>
                                                        </div>
                                                    )}
                                                    {g.address && (
                                                        <div className="col-span-2">
                                                            العنوان:{" "}
                                                            <span className="text-gray-700">
                                                                {g.address}
                                                            </span>
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
}
