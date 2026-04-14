"use client";

import { useState, useEffect } from "react";
import axios from "axios";
import { Loader2, Calendar, BookOpen, User, MapPin, Search, ArrowRight } from "lucide-react";
import { useRouter } from "next/navigation";

export default function ClassSchedulesPage() {
    const [schedules, setSchedules] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [searchTerm, setSearchTerm] = useState("");
    const router = useRouter();

    const days = ["saturday", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday"];
    const dayMap = {
        "saturday": "السبت",
        "sunday": "الأحد",
        "monday": "الاثنين",
        "tuesday": "الثلاثاء",
        "wednesday": "الأربعاء",
        "thursday": "الخميس",
        "friday": "الجمعة"
    };
    const periods = [1, 2, 3, 4, 5];

    useEffect(() => {
        fetchSchedules();
    }, []);

    const fetchSchedules = async () => {
        try {
            setLoading(true);
            const authStr = localStorage.getItem("auth");
            if (!authStr) {
                router.push("/login");
                return;
            }
            const auth = JSON.parse(authStr);

            const response = await axios.get(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules`,
                {
                    headers: { Authorization: `Bearer ${auth?.token}` },
                }
            );
            setSchedules(response.data.data || []);
        } catch (err) {
            console.error("Fetch error:", err);
            setError("فشل جلب جداول الحصص.");
        } finally {
            setLoading(false);
        }
    };

    // تجميع الحصص حسب الشعبة (Batch)
    const groupedByBatch = schedules.reduce((acc, schedule) => {
        const batch = schedule.batch_subject?.batch;
        if (!batch) return acc;

        if (!acc[batch.id]) {
            acc[batch.id] = {
                id: batch.id,
                name: batch.name,
                schedules: []
            };
        }
        acc[batch.id].schedules.push(schedule);
        return acc;
    }, {});

    const filteredBatches = Object.values(groupedByBatch).filter(batch =>
        batch.name.toLowerCase().includes(searchTerm.toLowerCase())
    );

    if (loading) return (
        <div className="flex flex-col items-center justify-center min-vh-100">
            <Loader2 className="w-10 h-10 text-[#AD164C] animate-spin" />
            <p className="mt-4 text-gray-600">جاري جلب الجداول الرسمية المعتمدة...</p>
        </div>
    );

    return (
        <div className="p-6">
            <div className="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex items-center gap-4">
                    <button onClick={() => router.back()} className="p-2 hover:bg-gray-100 rounded-full transition">
                        <ArrowRight className="w-6 h-6 text-gray-600" />
                    </button>
                    <div>
                        <h1 className="text-2xl font-bold text-gray-800 tracking-tight">برنامج الدوام الرسمي</h1>
                        <p className="text-gray-500 mt-1">عرض الجداول المعتمدة لجميع الشعب الدراسية</p>
                    </div>
                </div>
                <div className="relative w-full md:w-72">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        type="text"
                        placeholder="بحث عن شعبة..."
                        className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-pink-100 focus:border-[#AD164C] transition outline-none"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    />
                </div>
            </div>

            {filteredBatches.length === 0 ? (
                <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <Calendar className="w-16 h-16 text-gray-200 mx-auto mb-4" />
                    <h2 className="text-xl font-semibold text-gray-700">لا توجد جداول معتمدة حالياً</h2>
                    <p className="text-gray-500 mt-2">يمكنك اعتماد المسودات من قسم "مسودات الجدولة الذكية".</p>
                </div>
            ) : (
                <div className="space-y-12">
                    {filteredBatches.map(batch => (
                        <div key={batch.id}>
                            <div className="flex items-center gap-3 mb-4">
                                <div className="p-2 bg-pink-50 rounded-lg">
                                    <Calendar className="w-5 h-5 text-[#AD164C]" />
                                </div>
                                <h2 className="text-xl font-bold text-gray-800">{batch.name}</h2>
                            </div>

                            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="overflow-x-auto">
                                    <table className="w-full border-collapse">
                                        <thead>
                                            <tr className="bg-gray-50 border-b border-gray-100">
                                                <th className="p-4 text-gray-500 font-bold text-xs w-20">الحصة</th>
                                                {days.map(day => (
                                                    <th key={day} className="p-4 text-[#AD164C] font-bold text-center text-xs border-l border-gray-50">
                                                        {dayMap[day]}
                                                    </th>
                                                ))}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {periods.map(period => (
                                                <tr key={period} className="border-b border-gray-50">
                                                    <td className="p-4 bg-gray-50/30 text-gray-700 font-bold text-center border-l border-gray-100">
                                                        {period}
                                                    </td>
                                                    {days.map(day => {
                                                        const slot = batch.schedules.find(s =>
                                                            s.day_of_week?.toLowerCase() === day.toLowerCase() &&
                                                            s.period_number === period
                                                        );
                                                        return (
                                                            <td key={`${day}-${period}`} className="p-2 border-l border-gray-50 min-h-[100px] w-48 align-top">
                                                                {slot ? (
                                                                    <div className="h-full rounded-xl bg-gradient-to-br from-white to-pink-50/20 p-3 border border-pink-100/30 shadow-sm">
                                                                        <div className="flex flex-col gap-1.5">
                                                                            <div className="flex items-center gap-2 text-[#AD164C] font-bold text-sm">
                                                                                <BookOpen className="w-3.5 h-3.5" />
                                                                                <span className="truncate">{slot.batch_subject?.subject?.name}</span>
                                                                            </div>
                                                                            <div className="flex items-center gap-2 text-gray-600 text-[11px] font-medium">
                                                                                <User className="w-3 h-3 text-gray-400" />
                                                                                <span className="truncate">{slot.batch_subject?.instructor_subject?.instructor?.name || "مدرس"}</span>
                                                                            </div>
                                                                            <div className="flex items-center gap-2 text-gray-400 text-[10px]">
                                                                                <MapPin className="w-2.5 h-2.5" />
                                                                                <span className="truncate">{slot.class_room?.name || "قاعة"}</span>
                                                                            </div>
                                                                            <div className="mt-1 pt-1 border-t border-pink-50 text-[9px] text-gray-400 font-mono">
                                                                                {slot.start_time?.substring(0, 5)} - {slot.end_time?.substring(0, 5)}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                ) : (
                                                                    <div className="h-full min-h-[80px] flex items-center justify-center border-2 border-dashed border-gray-50 rounded-xl group hover:border-pink-50 transition cursor-pointer">
                                                                    </div>
                                                                )}
                                                            </td>
                                                        );
                                                    })}
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
