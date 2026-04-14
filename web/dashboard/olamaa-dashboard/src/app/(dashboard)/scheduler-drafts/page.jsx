"use client";

import { useState, useEffect } from "react";
import axios from "axios";
import { Loader2, Calendar, CheckCircle, AlertCircle, Sparkles, Trash2, CheckSquare, Square, X } from "lucide-react";
import Link from "next/link";

export default function SchedulerDraftsPage() {
    const [draftGroups, setDraftGroups] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [deletingId, setDeletingId] = useState(null);
    const [selectedIds, setSelectedIds] = useState([]);
    const [isBulkDeleting, setIsBulkDeleting] = useState(false);

    useEffect(() => {
        fetchDraftGroups();
    }, []);

    const fetchDraftGroups = async () => {
        try {
            setLoading(true);
            const authStr = localStorage.getItem("auth");
            if (!authStr) {
                throw new Error("لم يتم العثور على بيانات تسجيل الدخول");
            }
            const auth = JSON.parse(authStr);

            const response = await axios.get(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules/drafts`,
                {
                    headers: {
                        Authorization: `Bearer ${auth?.token}`,
                    },
                }
            );
            setDraftGroups(response.data.data);
            setSelectedIds([]);
        } catch (err) {
            console.error("Fetch error:", err);
            setError(`فشل جلب مسودات الجدولة: ${err.response?.data?.message || err.message}`);
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async (draftGroupId) => {
        if (!confirm("هل أنت متأكد من حذف هذه المسودة نهائياً؟")) return;

        try {
            setDeletingId(draftGroupId);
            const authStr = localStorage.getItem("auth");
            const auth = JSON.parse(authStr);

            await axios.delete(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules/drafts/${draftGroupId}`,
                {
                    headers: { Authorization: `Bearer ${auth?.token}` },
                }
            );

            setDraftGroups(prev => prev.filter(g => g.draft_group_id !== draftGroupId));
            setSelectedIds(prev => prev.filter(id => id !== draftGroupId));
        } catch (err) {
            alert("فشل حذف المسودة: " + (err.response?.data?.message || err.message));
        } finally {
            setDeletingId(null);
        }
    };

    const handleBulkDelete = async () => {
        if (selectedIds.length === 0) return;
        if (!confirm(`هل أنت متأكد من حذف ${selectedIds.length} مسودة نهائياً؟`)) return;

        try {
            setIsBulkDeleting(true);
            const authStr = localStorage.getItem("auth");
            const auth = JSON.parse(authStr);

            await axios.post(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules/drafts/bulk-delete`,
                { ids: selectedIds },
                {
                    headers: { Authorization: `Bearer ${auth?.token}` },
                }
            );

            setDraftGroups(prev => prev.filter(g => !selectedIds.includes(g.draft_group_id)));
            setSelectedIds([]);
        } catch (err) {
            alert("فشل حذف المسودات: " + (err.response?.data?.message || err.message));
        } finally {
            setIsBulkDeleting(false);
        }
    };

    const toggleSelect = (id) => {
        setSelectedIds(prev =>
            prev.includes(id) ? prev.filter(i => i !== id) : [...prev, id]
        );
    };

    const toggleSelectAll = () => {
        if (selectedIds.length === draftGroups.length) {
            setSelectedIds([]);
        } else {
            setSelectedIds(draftGroups.map(g => g.draft_group_id));
        }
    };

    if (loading) {
        return (
            <div className="flex flex-col items-center justify-center min-h-[400px]">
                <Loader2 className="w-10 h-10 text-[#AD164C] animate-spin" />
                <p className="mt-4 text-gray-600">جاري جلب المسودات المتاحة...</p>
            </div>
        );
    }

    return (
        <div className="p-6">
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800">مسودات الجدولة الذكية</h1>
                    <p className="text-gray-500 mt-1">هنا يمكنك مراجعة وتعديل المسودات التي ولدها الذكاء الاصطناعي</p>
                </div>
                <div className="flex items-center gap-3">
                    <Link
                        href="/class-schedules"
                        className="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg transition flex items-center gap-2"
                    >
                        <Calendar className="w-4 h-4 text-[#AD164C]" />
                        الجداول المعتمدة
                    </Link>
                    <button
                        onClick={fetchDraftGroups}
                        className="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition"
                    >
                        تحديث القائمة
                    </button>
                    <Link
                        href="/scheduler-wizard"
                        className="flex items-center gap-2 px-5 py-2 bg-gradient-to-l from-[#AD164C] to-[#D4296B] hover:shadow-lg hover:shadow-[#AD164C]/20 text-white rounded-lg transition-all duration-300 font-semibold"
                    >
                        <Sparkles className="w-4 h-4" />
                        توليد جدول جديد
                    </Link>
                </div>
            </div>

            {/* Bulk Actions Bar */}
            {draftGroups.length > 0 && (
                <div className="bg-white border border-gray-100 rounded-xl p-4 mb-6 flex justify-between items-center shadow-sm">
                    <div className="flex items-center gap-4">
                        <button
                            onClick={toggleSelectAll}
                            className="flex items-center gap-2 text-sm text-gray-600 hover:text-[#AD164C] transition"
                        >
                            {selectedIds.length === draftGroups.length ? (
                                <CheckSquare className="w-5 h-5 text-[#AD164C]" />
                            ) : (
                                <Square className="w-5 h-5" />
                            )}
                            اختيار الكل
                        </button>
                        {selectedIds.length > 0 && (
                            <span className="text-sm font-medium text-gray-500 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                                تم اختيار {selectedIds.length} مسودة
                            </span>
                        )}
                    </div>
                    {selectedIds.length > 0 && (
                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => setSelectedIds([])}
                                className="p-2 text-gray-400 hover:text-gray-600 transition"
                                title="إلغاء الاختيار"
                            >
                                <X className="w-5 h-5" />
                            </button>
                            <button
                                onClick={handleBulkDelete}
                                disabled={isBulkDeleting}
                                className="flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 rounded-lg transition font-semibold disabled:opacity-50"
                            >
                                {isBulkDeleting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Trash2 className="w-4 h-4" />}
                                حذف المحدد
                            </button>
                        </div>
                    )}
                </div>
            )}

            {error && (
                <div className="bg-red-50 border-r-4 border-red-500 p-4 mb-6 flex items-center gap-3 rounded-lg">
                    <AlertCircle className="text-red-500" />
                    <p className="text-red-700">{error}</p>
                </div>
            )}

            {draftGroups.length === 0 ? (
                <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <Calendar className="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h2 className="text-xl font-semibold text-gray-700">لا توجد مسودات حالياً</h2>
                    <p className="text-gray-500 mt-2 mb-6">يمكنك البدء بتوليد جدول جديد من خلال نظام الجدولة الذكي.</p>
                    <Link
                        href="/scheduler-wizard"
                        className="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-l from-[#AD164C] to-[#D4296B] text-white rounded-xl font-bold hover:shadow-xl hover:shadow-[#AD164C]/20 transition-all duration-300"
                    >
                        <Sparkles className="w-5 h-5" />
                        ابدأ التوليد الذكي
                    </Link>
                </div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {draftGroups.map((group) => (
                        <div
                            key={group.draft_group_id}
                            className={`bg-white rounded-xl shadow-sm border overflow-hidden transition relative group ${selectedIds.includes(group.draft_group_id) ? 'border-[#AD164C] ring-1 ring-[#AD164C]/20' : 'border-gray-100 hover:shadow-md'}`}
                        >
                            {/* Checkbox Overlay */}
                            <div
                                onClick={() => toggleSelect(group.draft_group_id)}
                                className={`absolute top-4 right-4 z-10 cursor-pointer p-1 rounded-md transition-all duration-200 ${selectedIds.includes(group.draft_group_id) ? 'bg-[#AD164C] text-white' : 'bg-white/80 text-gray-400 opacity-0 group-hover:opacity-100 shadow-sm border border-gray-100'}`}
                            >
                                {selectedIds.includes(group.draft_group_id) ? <CheckSquare className="w-5 h-5" /> : <Square className="w-5 h-5" />}
                            </div>

                            <div className="p-5 border-b border-gray-50 bg-[#FDF2F7]">
                                <div className="flex justify-between items-start mb-2 pr-8"> {/* Added padding for checkbox on the right */}
                                    <span className="p-1 px-2 bg-[#AD164C] text-white text-[10px] rounded uppercase font-bold tracking-wider">
                                        Smart Draft
                                    </span>
                                    <div className="flex items-center gap-2">
                                        <span className="text-xs text-gray-500">
                                            {new Date(group.created_at).toLocaleDateString('ar-EG', {
                                                year: 'numeric', month: 'long', day: 'numeric',
                                                hour: '2-digit', minute: '2-digit'
                                            })}
                                        </span>
                                        <button
                                            onClick={(e) => {
                                                e.preventDefault();
                                                handleDelete(group.draft_group_id);
                                            }}
                                            disabled={deletingId === group.draft_group_id}
                                            className="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-md transition disabled:opacity-50"
                                            title="حذف المسودة"
                                        >
                                            {deletingId === group.draft_group_id ? <Loader2 className="w-4 h-4 animate-spin" /> : <Trash2 className="w-4 h-4" />}
                                        </button>
                                    </div>
                                </div>
                                <h3 className="text-lg font-bold text-gray-800 truncate" title={group.draft_group_id}>
                                    {group.draft_group_id}
                                </h3>
                            </div>
                            <div className="p-5">
                                <div className="flex items-center justify-between mb-4">
                                    <div className="flex items-center gap-2">
                                        <CheckCircle className="w-4 h-4 text-green-500" />
                                        <span className="text-sm text-gray-600">
                                            {group.total_lessons} حصة مجدولة
                                        </span>
                                    </div>
                                </div>
                                <Link
                                    href={`/scheduler-drafts/${group.draft_group_id}`}
                                    className="block w-full text-center py-2.5 bg-[#AD164C] hover:bg-[#8D123E] text-white rounded-lg transition font-semibold"
                                >
                                    فتح ومعاينة الجدول
                                </Link>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
