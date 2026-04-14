"use client";

import { useState, useEffect } from "react";
import axios from "axios";
import { useRouter } from "next/navigation";
import {
    Loader2, CheckCircle2, AlertCircle, ChevronLeft, ChevronRight,
    Sparkles, BookOpen, Users, DoorOpen, Calendar, Zap, ArrowLeft,
    Settings2, ToggleLeft, ToggleRight, Clock, Layers, Ban, RefreshCw
} from "lucide-react";

const STEPS = [
    { id: 1, title: "اختيار الدفعات", icon: BookOpen },
    { id: 2, title: "مراجعة الموارد", icon: Users },
    { id: 3, title: "إعدادات التوليد", icon: Settings2 },
    { id: 4, title: "التوليد بالذكاء الاصطناعي", icon: Zap },
];

export default function SchedulerWizardPage() {
    const router = useRouter();
    const [currentStep, setCurrentStep] = useState(1);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Data
    const [setupData, setSetupData] = useState(null);
    const [selectedBatchIds, setSelectedBatchIds] = useState([]);

    // Generation Settings
    const [genSettings, setGenSettings] = useState({
        allow_empty_slots: true,
        empty_slot_penalty: 2,
        default_allow_same_subject_same_day: false,
        max_solutions_to_generate: 6,
        time_limit_seconds: 150,
        solver_workers: 4,
    });

    // Generation
    const [generating, setGenerating] = useState(false);
    const [generationResult, setGenerationResult] = useState(null);

    useEffect(() => {
        fetchSetupData();
    }, []);

    const fetchSetupData = async () => {
        try {
            setLoading(true);
            setError(null);
            const authStr = localStorage.getItem("auth");
            if (!authStr) throw new Error("يرجى تسجيل الدخول أولاً");
            const auth = JSON.parse(authStr);

            const response = await axios.get(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules/generate/setup`,
                { headers: { Authorization: `Bearer ${auth?.token}` } }
            );
            setSetupData(response.data.data);
        } catch (err) {
            console.error("Setup fetch error:", err);
            setError(`فشل جلب بيانات الإعداد: ${err.response?.data?.message || err.message}`);
        } finally {
            setLoading(false);
        }
    };

    const handleStartGeneration = async () => {
        try {
            setGenerating(true);
            setError(null);
            const auth = JSON.parse(localStorage.getItem("auth"));

            const response = await axios.post(
                `${process.env.NEXT_PUBLIC_API_BASE_URL}/class-schedules/generate/start`,
                { batch_ids: selectedBatchIds, config: genSettings },
                { headers: { Authorization: `Bearer ${auth?.token}` } }
            );
            setGenerationResult(response.data);
        } catch (err) {
            console.error("Generation error:", err);
            setError(`فشل التوليد: ${err.response?.data?.message || err.message}`);
        } finally {
            setGenerating(false);
        }
    };

    const toggleBatch = (batchId) => {
        setSelectedBatchIds(prev =>
            prev.includes(batchId)
                ? prev.filter(id => id !== batchId)
                : [...prev, batchId]
        );
    };

    const selectAllBatches = () => {
        if (!setupData) return;
        if (selectedBatchIds.length === setupData.batches.length) {
            setSelectedBatchIds([]);
        } else {
            setSelectedBatchIds(setupData.batches.map(b => b.id));
        }
    };

    // ===========================
    // LOADING STATE
    // ===========================
    if (loading) {
        return (
            <div className="flex flex-col items-center justify-center min-h-[500px]">
                <Loader2 className="w-12 h-12 text-[#AD164C] animate-spin" />
                <p className="mt-4 text-gray-500 text-lg">جاري تجهيز بيانات المعالج...</p>
            </div>
        );
    }

    // ===========================
    // SELECTED BATCHES DATA
    // ===========================
    const selectedBatches = setupData?.batches?.filter(b => selectedBatchIds.includes(b.id)) || [];
    const totalLessons = selectedBatches.reduce((sum, b) =>
        sum + b.subjects.reduce((s, sub) => s + (sub.weekly_lessons || 0), 0), 0
    );
    const uniqueInstructors = [...new Set(
        selectedBatches.flatMap(b => b.subjects.map(s => s.instructor_name)).filter(n => n !== "غير محدد")
    )];

    const validateConstraints = () => {
        const maxSlots = (setupData?.days?.length || 6) * (setupData?.slots?.length || 5);
        const errors = [];

        selectedBatches.forEach(batch => {
            const batchLessons = batch.subjects.reduce((sum, s) => sum + (s.weekly_lessons || 0), 0);
            if (batchLessons > maxSlots) {
                errors.push(`الشعبة "${batch.name}" لديها ${batchLessons} حصة، بينما سعة الجدول القصوى هي ${maxSlots} حصة (أيام × حصص).`);
            }

            const missingTeachers = batch.subjects.filter(s => s.weekly_lessons > 0 && (!s.instructor_id || s.instructor_name === "غير محدد"));
            if (missingTeachers.length > 0) {
                errors.push(`الشعبة "${batch.name}" تحتوي على مواد بدون مدرسين محددين: (${missingTeachers.map(m => m.name).join("، ")}). لا يمكن الجدولة بدون مدرس.`);
            }
        });

        // إذا كان ممنوع الفراغات والحصص أقل من السعة، سيحدث تعارض حتمي إذا لم نملأها
        if (!genSettings.allow_empty_slots) {
            selectedBatches.forEach(batch => {
                const batchLessons = batch.subjects.reduce((sum, s) => sum + (s.weekly_lessons || 0), 0);
                if (batchLessons < maxSlots) {
                    // لا نمنعها تماماً ولكن ننبه
                    console.warn(`تنبيه: الشعبة ${batch.name} لديها حصص أقل من السعة الكاملة مع خيار "منع الفتحات الفارغة" مفعل. قد يفشل المحرك.`);
                }
            });
        }

        return errors;
    };

    const handleStartGenerationWithValidation = async () => {
        const validationErrors = validateConstraints();
        if (validationErrors.length > 0) {
            setError(validationErrors.join(" | "));
            return;
        }
        setCurrentStep(4);
        handleStartGeneration();
    };

    // ===========================
    // RENDER
    // ===========================
    return (
        <div className="p-6 max-w-5xl mx-auto" dir="rtl">
            {/* Header */}
            <div className="mb-8">
                <button
                    onClick={() => router.push('/scheduler-drafts')}
                    className="flex items-center gap-1 text-gray-400 hover:text-gray-600 text-sm mb-3 transition"
                >
                    <ArrowLeft className="w-4 h-4" />
                    العودة لقائمة المسودات
                </button>
                <h1 className="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <Sparkles className="w-8 h-8 text-[#AD164C]" />
                    معالج الجدولة الذكية
                </h1>
                <p className="text-gray-500 mt-1">اتبع الخطوات لتوليد جدول مثالي باستخدام الذكاء الاصطناعي</p>
            </div>

            {/* Stepper */}
            <div className="flex items-center justify-between mb-10 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                {STEPS.map((step, i) => {
                    const Icon = step.icon;
                    const isActive = currentStep === step.id;
                    const isCompleted = currentStep > step.id;
                    return (
                        <div key={step.id} className="flex items-center flex-1">
                            <div className={`flex items-center gap-3 ${isActive ? 'text-[#AD164C]' : isCompleted ? 'text-green-600' : 'text-gray-400'}`}>
                                <div className={`w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 ${isActive ? 'bg-[#AD164C] text-white shadow-lg shadow-[#AD164C]/30' :
                                    isCompleted ? 'bg-green-500 text-white' :
                                        'bg-gray-200 text-gray-500'
                                    }`}>
                                    {isCompleted ? <CheckCircle2 className="w-5 h-5" /> : <Icon className="w-5 h-5" />}
                                </div>
                                <span className={`text-sm font-semibold hidden md:block ${isActive ? 'text-[#AD164C]' : ''}`}>
                                    {step.title}
                                </span>
                            </div>
                            {i < STEPS.length - 1 && (
                                <div className={`flex-1 h-0.5 mx-4 rounded transition-all duration-500 ${isCompleted ? 'bg-green-400' : 'bg-gray-200'
                                    }`} />
                            )}
                        </div>
                    );
                })}
            </div>

            {/* Error */}
            {error && (
                <div className="bg-red-50 border-r-4 border-red-500 p-4 mb-6 rounded-lg flex items-center gap-3">
                    <AlertCircle className="text-red-500 shrink-0" />
                    <p className="text-red-700 text-sm">{error}</p>
                </div>
            )}

            {/* ======================== STEP 1: Batch Selection ======================== */}
            {currentStep === 1 && (
                <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-6 border-b border-gray-100 bg-gradient-to-l from-[#FDF2F7] to-white">
                        <div className="flex justify-between items-center">
                            <div>
                                <h2 className="text-xl font-bold text-gray-800">اختر الدفعات للجدولة</h2>
                                <p className="text-gray-500 text-sm mt-1">
                                    يمكنك اختيار عدة دفعات ليتم جدولتها معاً لتجنب تعارض المدرسين
                                </p>
                            </div>
                            <button
                                onClick={selectAllBatches}
                                className="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition font-medium"
                            >
                                {selectedBatchIds.length === setupData?.batches?.length ? 'إلغاء الكل' : 'تحديد الكل'}
                            </button>
                        </div>
                    </div>

                    <div className="p-6">
                        {setupData?.batches?.length === 0 ? (
                            <div className="text-center py-10 text-gray-400">
                                <Calendar className="w-16 h-16 mx-auto mb-3 opacity-30" />
                                <p>لا توجد دفعات نشطة حالياً</p>
                            </div>
                        ) : (
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {setupData?.batches?.map(batch => {
                                    const isSelected = selectedBatchIds.includes(batch.id);
                                    const subjectsWithLessons = batch.subjects.filter(s => s.weekly_lessons > 0);
                                    return (
                                        <div
                                            key={batch.id}
                                            onClick={() => toggleBatch(batch.id)}
                                            className={`cursor-pointer rounded-xl border-2 p-4 transition-all duration-200 ${isSelected
                                                ? 'border-[#AD164C] bg-[#FDF2F7] shadow-md'
                                                : 'border-gray-200 hover:border-gray-300 hover:shadow-sm'
                                                }`}
                                        >
                                            <div className="flex justify-between items-start mb-3">
                                                <div>
                                                    <h3 className="font-bold text-gray-800">{batch.name}</h3>
                                                    <p className="text-xs text-gray-500 mt-0.5">
                                                        {batch.branch || 'بدون فرع'} • {batch.room || 'بدون قاعة'}
                                                    </p>
                                                </div>
                                                <div className={`w-6 h-6 rounded-full border-2 flex items-center justify-center transition ${isSelected ? 'bg-[#AD164C] border-[#AD164C]' : 'border-gray-300'
                                                    }`}>
                                                    {isSelected && <CheckCircle2 className="w-4 h-4 text-white" />}
                                                </div>
                                            </div>
                                            <div className="flex flex-wrap gap-1.5">
                                                {subjectsWithLessons.map(sub => (
                                                    <span key={sub.id} className="text-[10px] bg-white border border-gray-200 rounded-full px-2 py-0.5 text-gray-600">
                                                        {sub.name} ({sub.weekly_lessons}ح)
                                                    </span>
                                                ))}
                                                {subjectsWithLessons.length === 0 && (
                                                    <span className="text-xs text-orange-500">⚠️ لا توجد مواد بحصص مسندة</span>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </div>

                    {/* Footer */}
                    <div className="p-6 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                        <p className="text-sm text-gray-600">
                            تم اختيار <strong className="text-[#AD164C]">{selectedBatchIds.length}</strong> دفعة
                        </p>
                        <button
                            onClick={() => setCurrentStep(2)}
                            disabled={selectedBatchIds.length === 0}
                            className={`flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white transition ${selectedBatchIds.length > 0
                                ? 'bg-[#AD164C] hover:bg-[#8D123E] shadow-lg shadow-[#AD164C]/20'
                                : 'bg-gray-300 cursor-not-allowed'
                                }`}
                        >
                            التالي
                            <ChevronLeft className="w-4 h-4" />
                        </button>
                    </div>
                </div>
            )}

            {/* ======================== STEP 2: Resource Review ======================== */}
            {currentStep === 2 && (
                <div className="space-y-6">
                    {/* Summary Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div className="flex items-center gap-3 mb-2">
                                <div className="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <BookOpen className="w-5 h-5 text-blue-600" />
                                </div>
                                <span className="text-sm text-gray-500">إجمالي الحصص</span>
                            </div>
                            <p className="text-3xl font-bold text-gray-800">{totalLessons}</p>
                            <p className="text-xs text-gray-400 mt-1">حصة أسبوعية</p>
                        </div>
                        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div className="flex items-center gap-3 mb-2">
                                <div className="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <Users className="w-5 h-5 text-purple-600" />
                                </div>
                                <span className="text-sm text-gray-500">عدد المدرسين</span>
                            </div>
                            <p className="text-3xl font-bold text-gray-800">{uniqueInstructors.length}</p>
                            <p className="text-xs text-gray-400 mt-1">مدرس مشارك</p>
                        </div>
                        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div className="flex items-center gap-3 mb-2">
                                <div className="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                    <DoorOpen className="w-5 h-5 text-emerald-600" />
                                </div>
                                <span className="text-sm text-gray-500">القاعات المطلوبة</span>
                            </div>
                            <p className="text-3xl font-bold text-gray-800">{selectedBatches.length}</p>
                            <p className="text-xs text-gray-400 mt-1">قاعة</p>
                        </div>
                    </div>

                    {/* Batch Details */}
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100 bg-gradient-to-l from-[#FDF2F7] to-white">
                            <h2 className="text-xl font-bold text-gray-800">تفاصيل الدفعات المختارة</h2>
                            <p className="text-gray-500 text-sm mt-1">تأكد من صحة المعلومات قبل البدء بالتوليد</p>
                        </div>
                        <div className="divide-y divide-gray-100">
                            {selectedBatches.map(batch => (
                                <div key={batch.id} className="p-5">
                                    <div className="flex justify-between items-center mb-3">
                                        <h3 className="font-bold text-gray-800">{batch.name}</h3>
                                        <span className="text-xs bg-gray-100 px-3 py-1 rounded-full text-gray-500">
                                            {batch.room || 'بدون قاعة'}
                                        </span>
                                    </div>
                                    <div className="overflow-x-auto">
                                        <table className="w-full text-sm">
                                            <thead>
                                                <tr className="text-gray-400 text-xs">
                                                    <th className="text-right pb-2 font-medium">المادة</th>
                                                    <th className="text-center pb-2 font-medium">الحصص / أسبوع</th>
                                                    <th className="text-right pb-2 font-medium">المدرس</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {batch.subjects.filter(s => s.weekly_lessons > 0).map(sub => (
                                                    <tr key={sub.id} className="border-t border-gray-50">
                                                        <td className="py-2 text-gray-700">{sub.name}</td>
                                                        <td className="py-2 text-center">
                                                            <span className="bg-[#FDF2F7] text-[#AD164C] font-bold px-2 py-0.5 rounded text-xs">
                                                                {sub.weekly_lessons}
                                                            </span>
                                                        </td>
                                                        <td className="py-2 text-gray-600">
                                                            {sub.instructor_name === 'غير محدد' ? (
                                                                <span className="text-orange-500 text-xs">⚠️ غير محدد</span>
                                                            ) : sub.instructor_name}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Shared Instructors Warning */}
                    {selectedBatches.length > 1 && (() => {
                        const instructorBatches = {};
                        selectedBatches.forEach(b => {
                            b.subjects.forEach(s => {
                                if (s.instructor_id && s.instructor_name !== 'غير محدد') {
                                    if (!instructorBatches[s.instructor_id]) {
                                        instructorBatches[s.instructor_id] = { name: s.instructor_name, batches: [] };
                                    }
                                    if (!instructorBatches[s.instructor_id].batches.includes(b.name)) {
                                        instructorBatches[s.instructor_id].batches.push(b.name);
                                    }
                                }
                            });
                        });
                        const sharedInstructors = Object.values(instructorBatches).filter(i => i.batches.length > 1);
                        if (sharedInstructors.length === 0) return null;
                        return (
                            <div className="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                                <h3 className="font-bold text-amber-800 flex items-center gap-2 mb-3">
                                    <AlertCircle className="w-5 h-5" />
                                    مدرسون مشتركون بين عدة دفعات
                                </h3>
                                <p className="text-amber-700 text-sm mb-3">
                                    سيضمن النظام عدم تعارض جداولهم تلقائياً.
                                </p>
                                <div className="space-y-2">
                                    {sharedInstructors.map((inst, i) => (
                                        <div key={i} className="flex items-center gap-2 text-sm">
                                            <Users className="w-4 h-4 text-amber-600" />
                                            <strong className="text-amber-800">{inst.name}</strong>
                                            <span className="text-amber-600">→</span>
                                            <span className="text-amber-700">{inst.batches.join('، ')}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        );
                    })()}

                    {/* Footer */}
                    <div className="flex justify-between items-center">
                        <button
                            onClick={() => setCurrentStep(1)}
                            className="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition"
                        >
                            <ChevronRight className="w-4 h-4" />
                            السابق
                        </button>
                        <button
                            onClick={() => setCurrentStep(3)}
                            className="flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-[#AD164C] hover:bg-[#8D123E] shadow-lg shadow-[#AD164C]/20 transition"
                        >
                            التالي
                            <ChevronLeft className="w-4 h-4" />
                        </button>
                    </div>
                </div>
            )}

            {/* ======================== STEP 3: Generation Settings ======================== */}
            {currentStep === 3 && (
                <div className="space-y-6">
                    {/* Header Card */}
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100 bg-gradient-to-l from-[#FDF2F7] to-white">
                            <h2 className="text-xl font-bold text-gray-800 flex items-center gap-2">
                                <Settings2 className="w-5 h-5 text-[#AD164C]" />
                                إعدادات التوليد
                            </h2>
                            <p className="text-gray-500 text-sm mt-1">تحكم بسلوك محرك الذكاء الاصطناعي أثناء البحث عن الجدول المثالي</p>
                        </div>

                        <div className="p-6 space-y-6">
                            {/* Toggle: Allow Empty Slots */}
                            <div className="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition">
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                                        <Layers className="w-5 h-5 text-blue-500" />
                                    </div>
                                    <div>
                                        <p className="font-semibold text-gray-800">السماح بفترات فارغة</p>
                                        <p className="text-xs text-gray-400 mt-0.5">يسمح للنظام بترك بعض الفترات فارغة بدلاً من فرض ملء كل الفترات</p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setGenSettings(s => ({ ...s, allow_empty_slots: !s.allow_empty_slots }))}
                                    className="transition-transform hover:scale-110"
                                >
                                    {genSettings.allow_empty_slots
                                        ? <ToggleRight className="w-10 h-10 text-[#AD164C]" />
                                        : <ToggleLeft className="w-10 h-10 text-gray-300" />
                                    }
                                </button>
                            </div>

                            {/* التنبيه الذكي للمستخدم حول السعة والحصص */}
                            {(() => {
                                const maxSlots = (setupData?.days?.length || 6) * (setupData?.slots?.length || 5);
                                const lowUtilizationBatches = selectedBatches.filter(b => {
                                    const total = b.subjects.reduce((sum, s) => sum + (s.weekly_lessons || 0), 0);
                                    return total < maxSlots;
                                });

                                if (lowUtilizationBatches.length > 0) {
                                    return (
                                        <div className="mx-4 p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-3">
                                            <div className="flex items-center gap-2 text-amber-800 font-bold text-sm">
                                                <AlertCircle className="w-5 h-5 text-amber-600" />
                                                تنبيه حول سعة الجدول
                                            </div>
                                            <p className="text-xs text-amber-700 leading-relaxed">
                                                لاحظنا أن عدد الحصص المطلوبة في بعض الدفعات (مثل: {lowUtilizationBatches.map(b => b.name).join('، ')})
                                                أقل من السعة الكاملة للأسبوع ({maxSlots} حصة).
                                            </p>
                                            <p className="text-[11px] text-amber-600 font-medium">
                                                💡 حتى لو اخترت "منع الفراغات"، ستظهر حصص فارغة في **نهاية الأسبوع** (مثلاً يوم الخميس)
                                                لأنه لا توجد مواد كافية لملء كامل الوقت المتاح.
                                            </p>
                                        </div>
                                    );
                                }
                                return null;
                            })()}

                            {!genSettings.allow_empty_slots && (
                                <div className="mx-4 mt-1 p-3 bg-blue-50 border-r-4 border-blue-400 rounded-lg flex items-start gap-2 animate-in fade-in slide-in-from-top-1">
                                    <AlertCircle className="w-4 h-4 text-blue-500 mt-0.5 shrink-0" />
                                    <div>
                                        <p className="text-xs text-blue-800 font-medium">نظام التتابع مفعل</p>
                                        <p className="text-[10px] text-blue-600 mt-0.5 leading-relaxed">
                                            سيقوم النظام بجمع الحصص بالتتالي بدءاً من أول يوم (السبت) وأول حصة،
                                            لضمان جدول متراص بدون فجوات في منتصف اليوم.
                                        </p>
                                    </div>
                                </div>
                            )}

                            {/* Slider: Empty Slot Penalty */}
                            {genSettings.allow_empty_slots && (
                                <div className="p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                                    <div className="flex items-center justify-between mb-3">
                                        <p className="text-sm font-medium text-gray-700">وزن عقوبة الفترة الفارغة</p>
                                        <span className="text-sm font-bold text-[#AD164C] bg-[#FDF2F7] px-3 py-1 rounded-full">{genSettings.empty_slot_penalty}</span>
                                    </div>
                                    <input
                                        type="range"
                                        min="1" max="20" step="1"
                                        value={genSettings.empty_slot_penalty}
                                        onChange={e => setGenSettings(s => ({ ...s, empty_slot_penalty: +e.target.value }))}
                                        className="w-full h-2 rounded-full appearance-none cursor-pointer accent-[#AD164C] bg-gray-200"
                                    />
                                    <div className="flex justify-between text-[10px] text-gray-400 mt-1 px-0.5">
                                        <span>مرن (كثير فراغات)</span>
                                        <span>صارم (أقل فراغات)</span>
                                    </div>
                                </div>
                            )}

                            {/* Toggle: Same Subject Same Day */}
                            <div className="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition">
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                                        <Ban className="w-5 h-5 text-orange-500" />
                                    </div>
                                    <div>
                                        <p className="font-semibold text-gray-800">السماح بتكرار المادة في نفس اليوم</p>
                                        <p className="text-xs text-gray-400 mt-0.5">إذا تم التعطيل، لن تتكرر نفس المادة أكثر من مرة في اليوم الواحد</p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => setGenSettings(s => ({ ...s, default_allow_same_subject_same_day: !s.default_allow_same_subject_same_day }))}
                                    className="transition-transform hover:scale-110"
                                >
                                    {genSettings.default_allow_same_subject_same_day
                                        ? <ToggleRight className="w-10 h-10 text-[#AD164C]" />
                                        : <ToggleLeft className="w-10 h-10 text-gray-300" />
                                    }
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Solver Engine Settings */}
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100">
                            <h3 className="font-bold text-gray-800 flex items-center gap-2">
                                <Zap className="w-4 h-4 text-[#AD164C]" />
                                إعدادات المحرك
                            </h3>
                        </div>
                        <div className="p-6 space-y-6">
                            {/* Slider: Max Solutions */}
                            <div>
                                <div className="flex items-center justify-between mb-3">
                                    <div className="flex items-center gap-2">
                                        <RefreshCw className="w-4 h-4 text-purple-500" />
                                        <p className="text-sm font-medium text-gray-700">عدد الحلول البديلة</p>
                                    </div>
                                    <span className="text-sm font-bold text-[#AD164C] bg-[#FDF2F7] px-3 py-1 rounded-full">{genSettings.max_solutions_to_generate}</span>
                                </div>
                                <input
                                    type="range"
                                    min="1" max="10" step="1"
                                    value={genSettings.max_solutions_to_generate}
                                    onChange={e => setGenSettings(s => ({ ...s, max_solutions_to_generate: +e.target.value }))}
                                    className="w-full h-2 rounded-full appearance-none cursor-pointer accent-[#AD164C] bg-gray-200"
                                />
                                <div className="flex justify-between text-[10px] text-gray-400 mt-1 px-0.5">
                                    <span>حل واحد (أسرع)</span>
                                    <span>10 حلول (أشمل)</span>
                                </div>
                            </div>

                            {/* Slider: Time Limit */}
                            <div>
                                <div className="flex items-center justify-between mb-3">
                                    <div className="flex items-center gap-2">
                                        <Clock className="w-4 h-4 text-emerald-500" />
                                        <p className="text-sm font-medium text-gray-700">الحد الزمني</p>
                                    </div>
                                    <span className="text-sm font-bold text-[#AD164C] bg-[#FDF2F7] px-3 py-1 rounded-full">{genSettings.time_limit_seconds} ثانية</span>
                                </div>
                                <input
                                    type="range"
                                    min="30" max="300" step="10"
                                    value={genSettings.time_limit_seconds}
                                    onChange={e => setGenSettings(s => ({ ...s, time_limit_seconds: +e.target.value }))}
                                    className="w-full h-2 rounded-full appearance-none cursor-pointer accent-[#AD164C] bg-gray-200"
                                />
                                <div className="flex justify-between text-[10px] text-gray-400 mt-1 px-0.5">
                                    <span>30 ثانية (سريع)</span>
                                    <span>5 دقائق (دقيق)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="flex justify-between items-center">
                        <button
                            onClick={() => setCurrentStep(2)}
                            className="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition"
                        >
                            <ChevronRight className="w-4 h-4" />
                            السابق
                        </button>
                        <button
                            onClick={handleStartGenerationWithValidation}
                            className="flex items-center gap-2 px-8 py-3 rounded-xl font-bold text-white bg-gradient-to-l from-[#AD164C] to-[#D4296B] hover:shadow-xl hover:shadow-[#AD164C]/30 transition-all duration-300"
                        >
                            <Sparkles className="w-5 h-5" />
                            ابدأ التوليد الذكي
                        </button>
                    </div>
                </div>
            )}

            {/* ======================== STEP 4: Generation ======================== */}
            {currentStep === 4 && (
                <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-10 text-center">
                        {generating && (
                            <div className="space-y-6">
                                <div className="relative">
                                    <div className="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-[#AD164C] to-[#D4296B] flex items-center justify-center animate-pulse">
                                        <Sparkles className="w-12 h-12 text-white animate-spin" style={{ animationDuration: '3s' }} />
                                    </div>
                                </div>
                                <div>
                                    <h2 className="text-2xl font-bold text-gray-800">جاري التوليد...</h2>
                                    <p className="text-gray-500 mt-2">الذكاء الاصطناعي يعمل على إيجاد الجدول المثالي</p>
                                    <p className="text-gray-400 text-sm mt-1">قد تستغرق العملية من 10 إلى 30 ثانية</p>
                                </div>
                                <div className="w-64 mx-auto h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div className="h-full bg-gradient-to-l from-[#AD164C] to-[#D4296B] rounded-full animate-[loading_2s_ease-in-out_infinite]"
                                        style={{ width: '70%', animation: 'loading 2s ease-in-out infinite' }} />
                                </div>
                            </div>
                        )}

                        {!generating && generationResult?.success && (
                            <div className="space-y-6">
                                <div className="w-20 h-20 mx-auto rounded-full bg-green-100 flex items-center justify-center">
                                    <CheckCircle2 className="w-12 h-12 text-green-600" />
                                </div>
                                <div>
                                    <h2 className="text-2xl font-bold text-green-800">تم التوليد بنجاح! 🎉</h2>
                                    <p className="text-gray-500 mt-2">
                                        تم إيجاد <strong className="text-[#AD164C]">{generationResult.total_solutions}</strong> {generationResult.total_solutions === 1 ? 'حل' : 'حلول'} — اختر الأنسب
                                    </p>
                                </div>

                                {/* Solutions Grid */}
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-w-4xl mx-auto text-right" dir="rtl">
                                    {generationResult.drafts?.map((draft, idx) => {
                                        const isBest = idx === 0;
                                        return (
                                            <div
                                                key={draft.draft_group_id}
                                                className={`rounded-xl border-2 p-5 transition-all hover:shadow-lg ${isBest
                                                    ? 'border-[#AD164C] bg-[#FDF2F7] shadow-md'
                                                    : 'border-gray-200 bg-white hover:border-gray-300'
                                                    }`}
                                            >
                                                {/* Header */}
                                                <div className="flex items-center justify-between mb-4">
                                                    <div className="flex items-center gap-2">
                                                        <div className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold ${isBest ? 'bg-[#AD164C] text-white' : 'bg-gray-200 text-gray-600'
                                                            }`}>
                                                            {draft.solution_number}
                                                        </div>
                                                        <span className="font-bold text-gray-800">حل {draft.solution_number}</span>
                                                    </div>
                                                    {isBest && (
                                                        <span className="text-[10px] bg-[#AD164C] text-white px-2 py-0.5 rounded-full font-bold">
                                                            ⭐ الأفضل
                                                        </span>
                                                    )}
                                                </div>

                                                {/* Stats */}
                                                <div className="space-y-2 mb-4">
                                                    <div className="flex justify-between text-sm">
                                                        <span className="text-gray-500">الحصص المجدولة</span>
                                                        <span className="font-bold text-green-700">{draft.total_lessons}</span>
                                                    </div>
                                                    <div className="flex justify-between text-sm">
                                                        <span className="text-gray-500">حصص معلقة</span>
                                                        <span className={`font-bold ${draft.unassigned > 0 ? 'text-orange-600' : 'text-green-700'}`}>
                                                            {draft.unassigned}
                                                        </span>
                                                    </div>
                                                </div>

                                                {/* Action */}
                                                <button
                                                    onClick={() => router.push(`/scheduler-drafts/${draft.draft_group_id}`)}
                                                    className={`w-full py-2.5 rounded-lg font-semibold text-sm transition ${isBest
                                                        ? 'bg-[#AD164C] text-white hover:bg-[#8D123E]'
                                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                                        }`}
                                                >
                                                    <Calendar className="w-4 h-4 inline ml-1" />
                                                    معاينة الجدول
                                                </button>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        )}

                        {!generating && generationResult && !generationResult.success && (
                            <div className="space-y-6">
                                <div className="w-24 h-24 mx-auto rounded-full bg-red-100 flex items-center justify-center">
                                    <AlertCircle className="w-14 h-14 text-red-500" />
                                </div>
                                <div>
                                    <h2 className="text-2xl font-bold text-red-800">فشل التوليد</h2>
                                    <p className="text-gray-500 mt-2">{generationResult.message}</p>
                                </div>
                                <button
                                    onClick={() => { setCurrentStep(1); setGenerationResult(null); setError(null); }}
                                    className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                >
                                    <ChevronRight className="w-4 h-4" />
                                    العودة وإعادة المحاولة
                                </button>
                            </div>
                        )}

                        {!generating && !generationResult && error && (
                            <div className="space-y-6">
                                <div className="w-24 h-24 mx-auto rounded-full bg-red-100 flex items-center justify-center">
                                    <AlertCircle className="w-14 h-14 text-red-500" />
                                </div>
                                <div>
                                    <h2 className="text-2xl font-bold text-red-800">حدث خطأ</h2>
                                    <p className="text-gray-500 mt-2">{error}</p>
                                </div>
                                <button
                                    onClick={() => { setCurrentStep(1); setError(null); }}
                                    className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                >
                                    <ChevronRight className="w-4 h-4" />
                                    العودة
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            )}

            <style jsx>{`
                @keyframes loading {
                    0% { transform: translateX(100%); }
                    50% { transform: translateX(0%); }
                    100% { transform: translateX(-100%); }
                }
            `}</style>
        </div>
    );
}
