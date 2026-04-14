"use client";

import { useState, useEffect, useMemo } from "react";
import { useParams, useRouter } from "next/navigation";
import axios from "axios";
import { 
    Loader2, ArrowRight, Save, Trash2, AlertCircle, MapPin, User, BookOpen, 
    HandIcon, GripHorizontal, CheckCircle2, RotateCcw
} from "lucide-react";
import { 
    DndContext, 
    useDraggable, 
    useDroppable, 
    PointerSensor,
    useSensor,
    useSensors,
    DragOverlay,
    defaultDropAnimationSideEffects
} from "@dnd-kit/core";
import { CSS } from "@dnd-kit/utilities";
import { toast } from "react-hot-toast";

// ==========================================
// SUB-COMPONENTS for Drag & Drop
// ==========================================

const DraggableDraftCard = ({ draft, isDragging, isConflict }) => {
    const { attributes, listeners, setNodeRef, transform } = useDraggable({
        id: `draft-${draft.id}`,
        data: { draft }
    });

    const style = {
        transform: CSS.Translate.toString(transform),
        opacity: isDragging ? 0.3 : 1,
        cursor: 'grab'
    };

    return (
        <div 
            ref={setNodeRef} 
            style={style} 
            {...listeners} 
            {...attributes}
            className={`h-full rounded-xl p-3 shadow-sm border transition group overflow-hidden relative ${
                isConflict 
                    ? 'bg-red-50 border-red-200' 
                    : 'bg-gradient-to-br from-white to-pink-50/30 border-pink-100/50 hover:shadow-md'
            }`}
        >
            <div className={`absolute top-0 right-0 w-1 h-full opacity-30 group-hover:opacity-100 transition-opacity ${
                isConflict ? 'bg-red-500' : 'bg-[#AD164C]'
            }`}></div>
            
            <div className="flex flex-col gap-2">
                <div className={`flex items-center gap-2 font-bold text-sm ${isConflict ? 'text-red-700' : 'text-[#AD164C]'}`}>
                    <BookOpen className="w-3.5 h-3.5" />
                    <span className="truncate">{draft.batch_subject?.subject?.name}</span>
                </div>
                <div className="flex items-center gap-2 text-gray-600 text-xs font-medium">
                    <User className="w-3 h-3 text-gray-400" />
                    <span className="truncate">{draft.batch_subject?.instructor_subject?.instructor?.name || "غير محدد"}</span>
                </div>
                <div className="flex items-center gap-2 text-gray-500 text-[11px]">
                    <MapPin className="w-3 h-3 text-gray-300" />
                    <span className="truncate">{draft.class_room?.name || "قاعة افتراضية"}</span>
                </div>
                
                {isConflict && (
                    <div className="mt-1 text-[10px] text-red-500 flex items-center gap-1 font-bold">
                        <AlertCircle className="w-2.5 h-2.5" />
                        <span>تعارض!</span>
                    </div>
                )}
            </div>
            
            <div className="absolute bottom-1 left-1 opacity-0 group-hover:opacity-30 transition">
                <GripHorizontal className="w-3 h-3" />
            </div>
        </div>
    );
};

const DroppableSlot = ({ day, period, batchId, children }) => {
    const { isOver, setNodeRef } = useDroppable({
        id: `slot-${batchId}-${day}-${period}`,
        data: { day, period, batchId }
    });

    return (
        <td 
            ref={setNodeRef} 
            className={`p-2 border-l border-gray-50 min-h-[120px] w-48 align-top transition-colors duration-200 ${
                isOver ? 'bg-pink-50/50 border-pink-200' : ''
            }`}
        >
            {children}
        </td>
    );
};

// ==========================================
// MAIN PAGE COMPONENT
// ==========================================

export default function DraftDetailPage() {
    const params = useParams();
    const router = useRouter();
    const draftGroupId = params.draftGroupId;

    const [drafts, setDrafts] = useState([]);
    const [originalDrafts, setOriginalDrafts] = useState([]); // للمقارنة
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [saving, setSaving] = useState(false);
    const [publishing, setPublishing] = useState(false);
    const [activeId, setActiveId] = useState(null);

    const days = ["saturday", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday"];
    const periods = [1, 2, 3, 4, 5];

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: {
                distance: 8,
            },
        })
    );

    useEffect(() => {
        fetchDraftDetails();
    }, [draftGroupId]);

    const fetchDraftDetails = async () => {
        try {
            setLoading(true);
            const authStr = localStorage.getItem("auth");
            if (!authStr) return;
            const auth = JSON.parse(authStr);
            const response = await axios.get(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules/drafts/${draftGroupId}`,
                { headers: { Authorization: `Bearer ${auth.token}` } }
            );
            const data = response.data.data;
            setDrafts(data);
            setOriginalDrafts(JSON.parse(JSON.stringify(data)));
        } catch (err) {
            setError("فشل جلب تفاصيل المسودة.");
        } finally {
            setLoading(false);
        }
    };

    const handleSave = async () => {
        try {
            setSaving(true);
            const auth = JSON.parse(localStorage.getItem("auth"));
            
            const updates = drafts.map(d => ({
                id: d.id,
                day: d.day_of_week,
                period: d.period_number
            }));

            await axios.put(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules/drafts/${draftGroupId}/sync`,
                { updates },
                { headers: { Authorization: `Bearer ${auth.token}` } }
            );
            
            toast.success("تم حفظ التعديلات بنجاح");
            setOriginalDrafts(JSON.parse(JSON.stringify(drafts)));
        } catch (err) {
            console.error(err);
            toast.error("فشل حفظ التعديلات");
        } finally {
            setSaving(false);
        }
    };

    const handlePublish = async () => {
        try {
            setPublishing(true);
            const auth = JSON.parse(localStorage.getItem("auth"));
            await axios.post(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules/drafts/${draftGroupId}/publish`,
                {},
                { headers: { Authorization: `Bearer ${auth.token}` } }
            );
            toast.success("تم اعتماد الجدول بنجاح!");
            router.push("/class-schedules");
        } catch (err) {
            toast.error("فشل اعتماد الجدول.");
        } finally {
            setPublishing(false);
        }
    };

    const handleDragStart = (event) => {
        setActiveId(event.active.id);
    };

    const handleDragEnd = (event) => {
        const { active, over } = event;
        setActiveId(null);

        if (over && active.id !== over.id) {
            const dragData = active.data.current;
            const dropData = over.data.current;

            if (!dragData || !dropData) return;

            const movedDraft = dragData.draft;
            const { day, period } = dropData;

            // تحديث حالة المسودات محلياً
            setDrafts(prev => prev.map(d => {
                if (d.id === movedDraft.id) {
                    return { 
                        ...d, 
                        day_of_week: day, 
                        period_number: period,
                        // عند النقل يدوياً، نسمح للمستخدم بالتحكم ونزيل حالة التعارض القديمة
                        is_conflict: false,
                        conflict_message: null
                    };
                }
                // إذا كان هناك مادة في المكان المستهدف، يمكننا تبديل الأماكن أو تركها (المسودات الحالية لا تتبادل تلقائياً لتبسيط المنطق)
                return d;
            }));
        }
    };

    const getSlotDraft = (day, period, batchId) => {
        return drafts.find(d =>
            d.day_of_week?.toLowerCase() === day.toLowerCase() &&
            d.period_number === period &&
            d.batch_subject?.batch?.id === batchId
        );
    };

    // تجميع المسودات حسب الشعبة
    const groupedBatches = useMemo(() => {
        return drafts.reduce((acc, draft) => {
            const batch = draft.batch_subject?.batch;
            if (batch && !acc[batch.id]) {
                acc[batch.id] = {
                    id: batch.id,
                    name: batch.name,
                    branch: batch.institute_branch?.name,
                    lessons: []
                };
            }
            if (batch) acc[batch.id].lessons.push(draft);
            return acc;
        }, {});
    }, [drafts]);

    const isDirty = JSON.stringify(drafts) !== JSON.stringify(originalDrafts);

    if (loading) return (
        <div className="flex flex-col items-center justify-center min-h-[400px]">
            <Loader2 className="w-10 h-10 text-[#AD164C] animate-spin" />
            <p className="mt-4 text-gray-600">جاري بناء الجداول لكل الشعب...</p>
        </div>
    );

    return (
        <div className="p-6 bg-gray-50/30 min-h-screen" dir="rtl">
            <div className="mb-6 flex items-center justify-between sticky top-0 z-20 bg-white/80 backdrop-blur-md p-4 -mx-4 rounded-b-2xl shadow-sm border-b border-gray-100">
                <div className="flex items-center gap-4">
                    <button
                        onClick={() => router.back()}
                        className="p-2 hover:bg-gray-100 rounded-full transition"
                    >
                        <ArrowRight className="w-6 h-6 text-gray-600" />
                    </button>
                    <div>
                        <h1 className="text-2xl font-bold text-gray-800 tracking-tight">معاينة وتعديل المسودة</h1>
                        <p className="text-xs text-gray-400 mt-1">يمكنك سحب وإفلات الحصص لتعديل الجدول يدوياً</p>
                    </div>
                </div>
                <div className="flex gap-3">
                    {isDirty && (
                        <button
                            onClick={handleSave}
                            disabled={saving}
                            className="flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg transition font-bold shadow-sm disabled:opacity-50"
                        >
                            {saving ? <Loader2 className="w-4 h-4 animate-spin text-[#AD164C]" /> : <RotateCcw className="w-4 h-4 text-blue-500" />}
                            حفظ التعديلات اليودية
                        </button>
                    )}
                    <button
                        onClick={handlePublish}
                        disabled={publishing || isDirty}
                        title={isDirty ? "يرجى حفظ التعديلات أولاً" : ""}
                        className="flex items-center gap-2 px-6 py-2.5 bg-[#AD164C] hover:bg-[#8D123E] text-white rounded-lg transition font-bold shadow-lg shadow-pink-100 disabled:opacity-50"
                    >
                        {publishing ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
                        اعتماد ونشر الجدول
                    </button>
                </div>
            </div>

            <DndContext 
                sensors={sensors}
                onDragStart={handleDragStart}
                onDragEnd={handleDragEnd}
            >
                {Object.values(groupedBatches).map((batch) => (
                    <div key={batch.id} className="mb-12">
                        <div className="flex items-center gap-3 mb-4">
                            <div className="w-2 h-8 bg-[#AD164C] rounded-full"></div>
                            <div>
                                <h2 className="text-xl font-bold text-gray-800">{batch.name}</h2>
                                <p className="text-xs text-gray-500">{batch.branch || "الفرع الرئيسي"}</p>
                            </div>
                        </div>

                        <div className="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                            <div className="overflow-x-auto">
                                <table className="w-full border-collapse">
                                    <thead>
                                        <tr className="bg-gray-50 border-b border-gray-100">
                                            <th className="p-4 text-gray-500 font-bold text-sm w-24">الحصة / اليوم</th>
                                            {days.map(day => (
                                                <th key={day} className="p-4 text-[#AD164C] font-black text-center text-sm uppercase tracking-widest border-l border-gray-50">
                                                    {day === "saturday" ? "السبت" :
                                                        day === "sunday" ? "الأحد" :
                                                            day === "monday" ? "الاثنين" :
                                                                day === "tuesday" ? "الثلاثاء" :
                                                                    day === "wednesday" ? "الأربعاء" :
                                                                        day === "thursday" ? "الخميس" : "الجمعة"}
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {periods.map(period => (
                                            <tr key={period} className="border-b border-gray-50">
                                                <td className="p-4 bg-gray-50/50 text-gray-800 font-black text-center border-l border-gray-100 text-lg">
                                                    {period}
                                                </td>
                                                {days.map(day => {
                                                    const draft = getSlotDraft(day, period, batch.id);
                                                    return (
                                                        <DroppableSlot key={`${day}-${period}`} day={day} period={period} batchId={batch.id}>
                                                            {draft ? (
                                                                <DraggableDraftCard draft={draft} isConflict={draft.is_conflict} />
                                                            ) : (
                                                                <div className="h-full min-h-[100px] flex items-center justify-center border-2 border-dashed border-gray-50 rounded-xl group hover:border-pink-50 transition cursor-pointer">
                                                                    <span className="text-gray-200 text-xs font-medium opacity-0 group-hover:opacity-100 transition-opacity">فارغ</span>
                                                                </div>
                                                            )}
                                                        </DroppableSlot>
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
                
                <DragOverlay dropAnimation={{
                    sideEffects: defaultDropAnimationSideEffects({
                        styles: {
                            active: {
                                opacity: '0.3',
                            },
                        },
                    }),
                }}>
                    {activeId ? (
                        <div className="rounded-xl bg-white p-3 shadow-2xl border-2 border-[#AD164C] w-48 opacity-90 scale-105 transition-transform">
                            {(() => {
                                const draft = drafts.find(d => `draft-${d.id}` === activeId);
                                return (
                                    <div className="flex flex-col gap-2">
                                        <div className="flex items-center gap-2 text-[#AD164C] font-bold text-sm">
                                            <BookOpen className="w-3.5 h-3.5" />
                                            <span>{draft?.batch_subject?.subject?.name}</span>
                                        </div>
                                        <div className="text-gray-400 text-[10px]">جاري النقل...</div>
                                    </div>
                                );
                            })()}
                        </div>
                    ) : null}
                </DragOverlay>
            </DndContext>
        </div>
    );
}
