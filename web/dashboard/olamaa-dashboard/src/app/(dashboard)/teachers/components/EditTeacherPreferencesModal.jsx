"use client";

import { X, Check, Save, AlertTriangle, Heart, Ban, Star } from "lucide-react";
import { useEffect, useState } from "react";
import { notify } from "@/lib/helpers/toastify";
import { useUpdateTeacherMutation } from "@/store/services/teachersApi";

const DAYS = [
    { id: "saturday", label: "السبت" },
    { id: "sunday", label: "الأحد" },
    { id: "monday", label: "الاثنين" },
    { id: "tuesday", label: "الثلاثاء" },
    { id: "wednesday", label: "الأربعاء" },
    { id: "thursday", label: "الخميس" },
    { id: "friday", label: "الجمعة" },
];

const SLOTS = [1, 2, 3, 4, 5];

export default function EditTeacherPreferencesModal({ isOpen, onClose, teacher }) {
    const [updateTeacher, { isLoading: isUpdating }] = useUpdateTeacherMutation();

    // State for all preferences
    const [priorityLevel, setPriorityLevel] = useState(2);
    const [blockedSlots, setBlockedSlots] = useState({});
    const [preferredDays, setPreferredDays] = useState([]);
    const [avoidDays, setAvoidDays] = useState([]);
    const [preferredSlots, setPreferredSlots] = useState([]);
    const [avoidSlots, setAvoidSlots] = useState([]);

    useEffect(() => {
        if (isOpen && teacher?.preferences) {
            const p = teacher.preferences;
            setPriorityLevel(p.priority_level || 2);
            setBlockedSlots(p.blocked_slots || {});
            setPreferredDays(p.preferred_days || []);
            setAvoidDays(p.avoid_days || []);
            setPreferredSlots(p.preferred_slots || []);
            setAvoidSlots(p.avoid_slots || []);
        } else {
            setPriorityLevel(2);
            setBlockedSlots({});
            setPreferredDays([]);
            setAvoidDays([]);
            setPreferredSlots([]);
            setAvoidSlots([]);
        }
    }, [isOpen, teacher]);

    const toggleSlotBlocked = (dayId, slotId) => {
        setBlockedSlots((prev) => {
            const daySlots = prev[dayId] || [];
            const isBlocked = daySlots.includes(slotId);
            const newDaySlots = isBlocked ? daySlots.filter((id) => id !== slotId) : [...daySlots, slotId];
            const newBlocked = { ...prev };
            if (newDaySlots.length > 0) newBlocked[dayId] = newDaySlots;
            else delete newBlocked[dayId];
            return newBlocked;
        });
    };

    const toggleDayPref = (dayId, type) => {
        if (type === 'preferred') {
            setPreferredDays(prev => prev.includes(dayId) ? prev.filter(d => d !== dayId) : [...prev, dayId]);
            setAvoidDays(prev => prev.filter(d => d !== dayId)); // Remove from avoid if adding to preferred
        } else {
            setAvoidDays(prev => prev.includes(dayId) ? prev.filter(d => d !== dayId) : [...prev, dayId]);
            setPreferredDays(prev => prev.filter(d => d !== dayId)); // Remove from preferred if adding to avoid
        }
    };

    const toggleSlotPref = (slotId, type) => {
        if (type === 'preferred') {
            setPreferredSlots(prev => prev.includes(slotId) ? prev.filter(s => s !== slotId) : [...prev, slotId]);
            setAvoidSlots(prev => prev.filter(s => s !== slotId));
        } else {
            setAvoidSlots(prev => prev.includes(slotId) ? prev.filter(s => s !== slotId) : [...prev, slotId]);
            setPreferredSlots(prev => prev.filter(s => s !== slotId));
        }
    };

    const handleSave = async () => {
        try {
            await updateTeacher({
                id: teacher.id,
                preferences: {
                    ...teacher.preferences,
                    priority_level: priorityLevel,
                    blocked_slots: blockedSlots,
                    preferred_days: preferredDays,
                    avoid_days: avoidDays,
                    preferred_slots: preferredSlots,
                    avoid_slots: avoidSlots,
                }
            }).unwrap();
            notify.success("تم تحديث تفضيلات المدرس بنجاح");
            onClose();
        } catch (err) {
            notify.error(err?.data?.message || "فشل تحديث التفضيلات");
        }
    };

    if (!isOpen || !teacher) return null;

    return (
        <div className="fixed inset-0 z-50 bg-black/40 flex justify-center items-center p-4" dir="rtl">
            <div className="w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[92vh]">
                {/* Header */}
                <div className="bg-[#AD164C] p-4 flex justify-between items-center text-white shrink-0">
                    <div className="flex items-center gap-3">
                        <h2 className="text-xl font-bold">التفضيلات المتقدمة: {teacher.name}</h2>
                    </div>
                    <button onClick={onClose} className="p-1 hover:bg-white/20 rounded-lg transition">
                        <X size={24} />
                    </button>
                </div>

                {/* Content */}
                <div className="p-6 overflow-y-auto flex-1 space-y-8">

                    {/* Section: Priority */}
                    <section>
                        <div className="flex items-center gap-2 mb-4">
                            <Star className="text-yellow-500" size={20} />
                            <h3 className="font-bold text-gray-800">أولوية المدرس (Priority Level)</h3>
                        </div>
                        <div className="bg-yellow-50 p-4 rounded-xl border border-yellow-100 mb-4">
                            <p className="text-xs text-yellow-800 leading-relaxed">
                                كلما ارتفعت الأولوية، زاد اهتمام النظام بتحقيق تفضيلات هذا المدرس على حساب الآخرين عند حدوث تعارض.
                            </p>
                        </div>
                        <div className="flex items-center gap-6 px-4">
                            {[1, 2, 3, 4, 5].map(level => (
                                <button
                                    key={level}
                                    onClick={() => setPriorityLevel(level)}
                                    className={`flex-1 py-3 rounded-xl border-2 transition-all font-bold text-sm
                    ${priorityLevel === level
                                            ? 'bg-yellow-500 border-yellow-600 text-white shadow-md scale-105'
                                            : 'bg-white border-gray-100 text-gray-400 hover:border-yellow-200'}`}
                                >
                                    {level === 1 ? 'منخفضة' : level === 5 ? 'قصوى' : `مستوى ${level}`}
                                </button>
                            ))}
                        </div>
                    </section>

                    <hr className="border-gray-100" />

                    {/* Section: Blocked Grid */}
                    <section>
                        <div className="flex items-center gap-2 mb-4">
                            <Ban className="text-red-500" size={20} />
                            <h3 className="font-bold text-gray-800">الأوقات المحظورة تماماً (Hard Constraints)</h3>
                        </div>
                        <div className="overflow-x-auto rounded-xl border border-gray-100">
                            <table className="w-full border-collapse">
                                <thead>
                                    <tr className="bg-gray-50/50">
                                        <th className="p-3 text-right text-gray-400 font-medium text-xs border-b border-gray-100 w-24">اليوم / الحصة</th>
                                        {SLOTS.map(slot => (
                                            <th key={slot} className="p-3 text-center text-gray-700 font-bold text-sm border-b border-gray-100">
                                                {slot}
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {DAYS.map(day => (
                                        <tr key={day.id} className="hover:bg-gray-50/30 transition">
                                            <td className="p-3 text-gray-700 font-bold text-xs border-b border-l border-gray-50 bg-gray-50/10">
                                                {day.label}
                                            </td>
                                            {SLOTS.map(slot => {
                                                const isBlocked = blockedSlots[day.id]?.includes(slot);
                                                return (
                                                    <td key={`${day.id}-${slot}`} className="p-1.5 border-b border-gray-50 text-center">
                                                        <button
                                                            onClick={() => toggleSlotBlocked(day.id, slot)}
                                                            className={`w-full h-10 rounded-lg border transition-all flex items-center justify-center
                                ${isBlocked
                                                                    ? 'bg-red-500 border-red-600 text-white shadow-sm'
                                                                    : 'bg-white border-gray-100 text-transparent hover:border-red-200 hover:text-red-300'
                                                                }`}
                                                        >
                                                            <Ban size={14} />
                                                        </button>
                                                    </td>
                                                );
                                            })}
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <hr className="border-gray-100" />

                    {/* Section: Day Preferences */}
                    <section className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div className="flex items-center gap-2 mb-4">
                                <Heart className="text-green-500" size={20} />
                                <h3 className="font-bold text-gray-800">الأيام المفضلة</h3>
                            </div>
                            <div className="flex flex-wrap gap-2">
                                {DAYS.map(day => (
                                    <button
                                        key={day.id}
                                        onClick={() => toggleDayPref(day.id, 'preferred')}
                                        className={`px-3 py-2 rounded-lg border transition-all text-xs font-medium
                      ${preferredDays.includes(day.id)
                                                ? 'bg-green-100 border-green-200 text-green-700'
                                                : 'bg-white border-gray-100 text-gray-400 hover:border-green-200'}`}
                                    >
                                        {day.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                        <div>
                            <div className="flex items-center gap-2 mb-4">
                                <AlertTriangle className="text-orange-500" size={20} />
                                <h3 className="font-bold text-gray-800">أيام يفضل تجنبها</h3>
                            </div>
                            <div className="flex flex-wrap gap-2">
                                {DAYS.map(day => (
                                    <button
                                        key={day.id}
                                        onClick={() => toggleDayPref(day.id, 'avoid')}
                                        className={`px-3 py-2 rounded-lg border transition-all text-xs font-medium
                      ${avoidDays.includes(day.id)
                                                ? 'bg-orange-100 border-orange-200 text-orange-700'
                                                : 'bg-white border-gray-100 text-gray-400 hover:border-orange-200'}`}
                                    >
                                        {day.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </section>

                    <hr className="border-gray-100" />

                    {/* Section: Slot Preferences */}
                    <section className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div className="flex items-center gap-2 mb-4">
                                <Heart className="text-green-500" size={20} />
                                <h3 className="font-bold text-gray-800">الحصص المفضلة</h3>
                            </div>
                            <div className="flex flex-wrap gap-2">
                                {SLOTS.map(slot => (
                                    <button
                                        key={slot}
                                        onClick={() => toggleSlotPref(slot, 'preferred')}
                                        className={`px-4 py-2 rounded-lg border transition-all text-xs font-medium
                      ${preferredSlots.includes(slot)
                                                ? 'bg-green-100 border-green-200 text-green-700'
                                                : 'bg-white border-gray-100 text-gray-400 hover:border-green-200'}`}
                                    >
                                        الحصة {slot}
                                    </button>
                                ))}
                            </div>
                        </div>
                        <div>
                            <div className="flex items-center gap-2 mb-4">
                                <AlertTriangle className="text-orange-500" size={20} />
                                <h3 className="font-bold text-gray-800">حصص يفضل تجنبها</h3>
                            </div>
                            <div className="flex flex-wrap gap-2">
                                {SLOTS.map(slot => (
                                    <button
                                        key={slot}
                                        onClick={() => toggleSlotPref(slot, 'avoid')}
                                        className={`px-4 py-2 rounded-lg border transition-all text-xs font-medium
                      ${avoidSlots.includes(slot)
                                                ? 'bg-orange-100 border-orange-200 text-orange-700'
                                                : 'bg-white border-gray-100 text-gray-400 hover:border-orange-200'}`}
                                    >
                                        الحصة {slot}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </section>

                </div>

                {/* Footer */}
                <div className="p-4 bg-gray-50 flex justify-end gap-3 shrink-0">
                    <button
                        onClick={onClose}
                        className="px-6 py-2 text-gray-600 hover:bg-gray-200 rounded-xl transition font-medium"
                    >
                        إلغاء
                    </button>
                    <button
                        onClick={handleSave}
                        disabled={isUpdating}
                        className="px-8 py-2 bg-[#AD164C] text-white rounded-xl hover:bg-[#8e123e] transition disabled:opacity-50 flex items-center gap-2 font-bold shadow-lg shadow-pink-100"
                    >
                        {isUpdating ? 'جاري الحفظ...' : (
                            <>
                                <Save size={18} />
                                حفظ التفضيلات
                            </>
                        )}
                    </button>
                </div>
            </div>
        </div>
    );
}
