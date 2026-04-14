"use client";

import { useEffect, useMemo, useState } from "react";
import { X, Send, MessageSquare, Bell, Paperclip, Trash2, Phone, AlertCircle } from "lucide-react";
import { notify } from "@/lib/helpers/toastify";

import FormInput from "@/components/common/InputField";
import SearchableSelect from "@/components/common/SearchableSelect";
import GradientButton from "@/components/common/GradientButton";

import { useGetInstituteBranchesQuery } from "@/store/services/instituteBranchesApi";
import { useGetBatchesQuery } from "@/store/services/batchesApi";
import { useGetStudentsDetailsQuery } from "@/store/services/studentsApi";
import { useGetStudentContactsSummaryQuery } from "@/store/services/contactsApi";
import { useSendSingleSmsMutation } from "@/store/services/messagesApi";
import { useCreateNotificationMutation } from "@/store/services/notificationsApi";
import { debugLogger } from "@/lib/helpers/debugLogger";

export default function MessageModal({ open, onClose }) {
  const [type, setType] = useState("sms"); // sms | notification

  // --- Common Data ---
  const { data: branchesRes } = useGetInstituteBranchesQuery(undefined, {
    skip: !open,
  });
  const { data: batchesRes } = useGetBatchesQuery(undefined, {
    skip: !open,
  });
  const { data: studentsRes } = useGetStudentsDetailsQuery({}, { skip: !open });

  const branches = useMemo(() => branchesRes?.data || [], [branchesRes]);
  const batches = useMemo(
    () => batchesRes?.data?.batches || batchesRes?.data || [],
    [batchesRes],
  );
  const students = useMemo(
    () => studentsRes?.data?.data || studentsRes?.data || [],
    [studentsRes],
  );

  const branchOptions = useMemo(
    () => branches.map((b) => ({ value: String(b.id), label: b.name })),
    [branches],
  );

  const batchOptions = useMemo(
    () => batches.map((b) => ({ value: String(b.id), label: b.name })),
    [batches],
  );

  const studentOptions = useMemo(
    () =>
      students.map((s) => ({
        value: String(s.id),
        label:
          s.full_name ||
          `${s.first_name || ""} ${s.last_name || ""}`.trim() ||
          `طالب #${s.id}`,
      })),
    [students],
  );

  // --- SMS State ---
  const [smsForm, setSmsForm] = useState({
    student_id: "",
    phone: "",
    message: "",
  });

  useEffect(() => {
    if (open) {
      setSmsForm({ student_id: "", phone: "", message: "" });
      setNotifForm({
        title: "",
        body: "",
        target_type: "all",
        branch_id: "",
        batch_id: "",
        student_id: "",
        attachments: [],
      });
    }
  }, [open]);

  const { data: contactsSummary, isFetching: loadingContacts } =
    useGetStudentContactsSummaryQuery(smsForm.student_id, {
      skip: !open || type !== "sms" || !smsForm.student_id,
    });

  const smsContactOptions = useMemo(() => {
    if (!contactsSummary?.data) return [];
    const data = contactsSummary.data;

    const allContacts = [
      ...(Array.isArray(data.personal_contacts) ? data.personal_contacts : []),
      ...(Array.isArray(data.family_contacts) ? data.family_contacts : []),
      ...(data?.full_family_summary?.primary_sms_contact
        ? [data.full_family_summary.primary_sms_contact]
        : []),
      ...(Array.isArray(data.guardians_contacts)
        ? data.guardians_contacts.flatMap((g) =>
            Array.isArray(g?.details) ? g.details : [],
          )
        : []),
    ];

    const uniqueMap = new Map();
    allContacts.forEach((c) => {
      if (!c || c.type !== "phone" || !c.supports_sms || !c.full_phone_number)
        return;
      // To avoid duplicates, we use a Map with the phone number as the key.
      if (!uniqueMap.has(c.full_phone_number)) {
        uniqueMap.set(c.full_phone_number, c);
      }
    });

    return Array.from(uniqueMap.values()).map((c) => ({
      value: c.full_phone_number,
      label: `${c.owner_type === "father" ? "الأب" : c.owner_type === "mother" ? "الأم" : "الطالب"}: \u202A${c.full_phone_number}\u202C`,
    }));
  }, [contactsSummary]);

  useEffect(() => {
    if (smsContactOptions.length > 0 && !smsForm.phone) {
      setSmsForm((p) => ({ ...p, phone: smsContactOptions[0].value }));
    }
  }, [smsContactOptions]);

  // --- Notification State ---
  const [notifForm, setNotifForm] = useState({
    title: "",
    body: "",
    target_type: "all", // all | branch | batch | custom
    branch_id: "",
    batch_id: "",
    student_id: "", // For single custom target
    attachments: [],
  });

  // --- Mutations ---
  const [sendSms, { isLoading: sendingSms }] = useSendSingleSmsMutation();
  const [createNotif, { isLoading: sendingNotif }] =
    useCreateNotificationMutation();

  const handleSendSms = async () => {
    if (!smsForm.phone) return notify.error("يرجى اختيار رقم هاتف");
    if (!smsForm.message.trim()) return notify.error("يرجى كتابة نص الرسالة");

    // Automatic language detection (Arabic contains 0600-06FF)
    const isArabic = /[\u0600-\u06FF]/.test(smsForm.message);

    try {
      await sendSms({
        phone: smsForm.phone,
        message: smsForm.message,
        lang: isArabic ? 1 : 0,
      }).unwrap();
      notify.success("تم إرسال الرسالة بنجاح");
      onClose();
    } catch (err) {
      debugLogger.error(err, "SMS Sending");
      notify.error(err?.data?.message || "فشل في إرسال الرسالة");
    }
  };

  const handleSendNotif = async () => {
    if (notifForm.title.trim().length < 3)
      return notify.error("عنوان الإشعار يجب أن لا يقل عن 3 أحرف");
    if (notifForm.body.trim().length < 5)
      return notify.error("محتوى الإشعار يجب أن لا يقل عن 5 أحرف");

    const formData = new FormData();
    formData.append("title", notifForm.title);
    formData.append("body", notifForm.body);
    formData.append("target_snapshot[type]", notifForm.target_type);

    if (notifForm.target_type === "branch") {
      if (!notifForm.branch_id) return notify.error("يرجى اختيار الفرع");
      formData.append("target_snapshot[branch_id]", notifForm.branch_id);
    } else if (notifForm.target_type === "batch") {
      if (!notifForm.batch_id) return notify.error("يرجى اختيار الشعبة");
      formData.append("target_snapshot[batch_id]", notifForm.batch_id);
    } else if (notifForm.target_type === "custom") {
      if (!notifForm.student_id) return notify.error("يرجى اختيار الطالب");
      const student = students.find(
        (s) => String(s.id) === String(notifForm.student_id),
      );
      const userId = student?.user_id || student?.family?.user_id;
      if (!userId)
        return notify.error(
          "هذا الطالب/العائلة لا يملك حساباً مسجلاً لتلقي الإشعارات",
        );
      formData.append("target_snapshot[user_ids][]", userId);
    }

    // Append attachments
    if (notifForm.attachments && notifForm.attachments.length > 0) {
      notifForm.attachments.forEach((file) => {
        formData.append("attachments[]", file);
      });
    }

    try {
      await createNotif(formData).unwrap();
      notify.success("تم إنشاء الإشعار بنجاح");
      onClose();
    } catch (err) {
      console.error("❌ Notification Creation Error:", err);
      debugLogger.error(err, "Notification Creation");
      notify.error(err?.data?.message || "فشل في إنشاء الإشعار");
    }
  };

  const handleFileChange = (e) => {
    const files = Array.from(e.target.files);
    const existingCount = notifForm.attachments.length;

    if (existingCount + files.length > 5) {
      return notify.error("لا يمكن رفع أكثر من 5 ملفات");
    }

    // Filter by size (10MB)
    const validFiles = files.filter((f) => f.size <= 10 * 1024 * 1024);
    if (validFiles.length < files.length) {
      notify.warning("تم استبعاد بعض الملفات التي تتجاوز حجم 10 ميجابايت");
    }

    setNotifForm((prev) => ({
      ...prev,
      attachments: [...prev.attachments, ...validFiles],
    }));
  };

  const removeAttachment = (index) => {
    setNotifForm((prev) => ({
      ...prev,
      attachments: prev.attachments.filter((_, i) => i !== index),
    }));
  };

  if (!open) return null;

  return (
    <div
      className="fixed inset-0 bg-black/40 z-50 flex justify-start backdrop-blur-md"
      onClick={onClose}
    >
      <div
        dir="rtl"
        className="w-full sm:w-[500px] bg-white h-full shadow-2xl flex flex-col"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/50">
          <div>
            <h2 className="text-[#6F013F] font-bold text-xl flex items-center gap-2">
              <MessageSquare className="w-5 h-5 text-[#D40078]" />
              إرسال رسالة
            </h2>
          </div>
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-400 hover:text-gray-600"
          >
            <X className="w-6 h-6" />
          </button>
        </div>

        {/* Type Selector (Radio Style) */}
        <div className="px-6 py-4 flex items-center gap-8 border-b border-gray-50">
          <label className="flex items-center gap-2.5 cursor-pointer group">
            <div className="relative flex items-center justify-center">
              <input
                type="radio"
                name="msgType"
                className="sr-only"
                checked={type === "sms"}
                onChange={() => setType("sms")}
              />
              <div className={`w-5 h-5 rounded-full border-2 transition-all flex items-center justify-center
                ${type === "sms" ? "border-[#6F013F]" : "border-gray-300 group-hover:border-gray-400"}`}
              >
                {type === "sms" && <div className="w-2.5 h-2.5 rounded-full bg-[#6F013F] animate-in zoom-in-50 duration-200" />}
              </div>
            </div>
            <span className={`text-sm font-bold transition-colors ${type === "sms" ? "text-gray-900" : "text-gray-500 group-hover:text-gray-700"}`}>
              رسالة SMS
            </span>
          </label>

          <label className="flex items-center gap-2.5 cursor-pointer group">
            <div className="relative flex items-center justify-center">
              <input
                type="radio"
                name="msgType"
                className="sr-only"
                checked={type === "notification"}
                onChange={() => setType("notification")}
              />
              <div className={`w-5 h-5 rounded-full border-2 transition-all flex items-center justify-center
                ${type === "notification" ? "border-[#6F013F]" : "border-gray-300 group-hover:border-gray-400"}`}
              >
                {type === "notification" && <div className="w-2.5 h-2.5 rounded-full bg-[#6F013F] animate-in zoom-in-50 duration-200" />}
              </div>
            </div>
            <span className={`text-sm font-bold transition-colors ${type === "notification" ? "text-gray-900" : "text-gray-500 group-hover:text-gray-700"}`}>
              إشعار (App)
            </span>
          </label>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto px-6 py-4 space-y-6">
          {type === "sms" ? (
            <div className="space-y-5">
              <SearchableSelect
                label="اختر الطالب"
                placeholder="ابحث عن طالب..."
                options={studentOptions}
                value={smsForm.student_id}
                onChange={(v) =>
                  setSmsForm((p) => ({ ...p, student_id: v, phone: "" }))
                }
                required
              />

              {smsForm.student_id && (
                <div className="space-y-2">
                  <label className="text-sm font-semibold text-gray-700">
                    رقم الهاتف (يدعم SMS)
                  </label>
                  {loadingContacts ? (
                    <div className="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-center gap-2 text-sm text-gray-500 animate-pulse h-[52px]">
                      <div className="w-4 h-4 border-2 border-gray-300 border-t-[#6F013F] rounded-full animate-spin" />
                      جارٍ تحميل الجهات...
                    </div>
                  ) : smsContactOptions.length === 1 ? (
                    <div className="p-4 bg-gray-50 rounded-2xl border border-gray-200 flex items-center justify-between group h-[52px]">
                      <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-full bg-[#6F013F]/5 flex items-center justify-center text-[#6F013F]">
                          <Phone className="w-4 h-4" />
                        </div>
                        <div>
                          <span className="text-sm font-bold text-gray-900">
                            {smsContactOptions[0].label}
                          </span>
                        </div>
                      </div>
                      <span className="text-[10px] bg-green-50 text-green-700 px-2 py-0.5 rounded-full border border-green-100 font-bold">
                        رقم مفعل
                      </span>
                    </div>
                  ) : smsContactOptions.length > 1 ? (
                    <SearchableSelect
                      placeholder="اختر الرقم..."
                      options={smsContactOptions}
                      value={smsForm.phone}
                      onChange={(v) => setSmsForm((p) => ({ ...p, phone: v }))}
                      required
                    />
                  ) : (
                    <div className="p-4 bg-red-50 rounded-2xl border border-red-100 flex items-center gap-2 text-sm text-red-600 h-[52px]">
                      <AlertCircle className="w-4 h-4" />
                      لا توجد أرقام تدعم SMS لهذا الطالب
                    </div>
                  )}
                </div>
              )}

              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <label className="text-sm font-semibold text-gray-700">
                    نص الرسالة
                  </label>
                  <div className="flex items-center gap-2">
                    <span className={`text-[10px] font-bold px-1.5 py-0.5 rounded ${/[\u0600-\u06FF]/.test(smsForm.message) ? "bg-[#6F013F]/10 text-[#6F013F]" : "bg-gray-100 text-gray-500"}`}>
                      {/[\u0600-\u06FF]/.test(smsForm.message) ? "عربي (Unicode)" : "إنجليزي (ASCII)"}
                    </span>
                    <span className="text-[11px] text-gray-400 font-mono">
                      {smsForm.message.length} حرف
                    </span>
                  </div>
                </div>
                <textarea
                  rows={8}
                  className="w-full border border-gray-200 rounded-2xl p-4 text-sm outline-none focus:border-[#D40078] focus:ring-2 focus:ring-[#D40078]/10 transition-all resize-none bg-gray-50/50"
                  placeholder="اكتب رسالتك النصية هنا..."
                  value={smsForm.message}
                  onChange={(e) =>
                    setSmsForm((p) => ({ ...p, message: e.target.value }))
                  }
                />
                <p className="text-[10px] text-gray-400 text-left">
                   {/[\u0600-\u06FF]/.test(smsForm.message) 
                    ? `جزء واحد: 70 حرف | الأجزاء المتوقعة: ${Math.ceil(smsForm.message.length / 67) || 1}`
                    : `جزء واحد: 160 حرف | الأجزاء المتوقعة: ${Math.ceil(smsForm.message.length / 153) || 1}`}
                </p>
              </div>
            </div>
          ) : (
            <div className="space-y-5">
              <FormInput
                label="عنوان الإشعار"
                placeholder="مثلاً: تنبيه بخصوص الامتحان"
                value={notifForm.title}
                onChange={(e) =>
                  setNotifForm((p) => ({ ...p, title: e.target.value }))
                }
                required
              />

              <div className="space-y-3">
                <label className="text-sm font-semibold text-gray-700">
                  توجيه الإشعار إلى
                </label>
                <div className="grid grid-cols-4 gap-2">
                  {["all", "branch", "batch", "custom"].map((t) => (
                    <button
                      key={t}
                      onClick={() =>
                        setNotifForm((p) => ({ ...p, target_type: t }))
                      }
                      className={`py-2 px-1 rounded-lg text-xs font-medium  transition-all
                        ${
                          notifForm.target_type === t
                            ? "border-[#6F013F] bg-[#6F013F]/5 text-[#6F013F]"
                            : "border-gray-200 text-gray-500 hover:border-gray-300"
                        }`}
                    >
                      {t === "all"
                        ? "الكل"
                        : t === "branch"
                          ? "فرع معين"
                          : t === "batch"
                            ? "شعبة معينة"
                            : "طالب محدد"}
                    </button>
                  ))}
                </div>
              </div>

              {notifForm.target_type === "branch" && (
                <SearchableSelect
                  label="اختر الفرع"
                  placeholder="اختر الفرع المستهدف..."
                  options={branchOptions}
                  value={notifForm.branch_id}
                  onChange={(v) =>
                    setNotifForm((p) => ({ ...p, branch_id: v }))
                  }
                  required
                />
              )}

              {notifForm.target_type === "batch" && (
                <SearchableSelect
                  label="اختر الشعبة"
                  placeholder="اختر الشعبة المستهدفة..."
                  options={batchOptions}
                  value={notifForm.batch_id}
                  onChange={(v) => setNotifForm((p) => ({ ...p, batch_id: v }))}
                  required
                />
              )}

              {notifForm.target_type === "custom" && (
                <SearchableSelect
                  label="اختر الطالب"
                  placeholder="ابحث عن طالب..."
                  options={studentOptions}
                  value={notifForm.student_id}
                  onChange={(v) =>
                    setNotifForm((p) => ({ ...p, student_id: v }))
                  }
                  required
                />
              )}

              <div className="space-y-2">
                <label className="text-sm font-semibold text-gray-700">
                  محتوى الإشعار
                </label>
                <textarea
                  rows={8}
                  className="w-full border border-gray-200 rounded-2xl p-4 text-sm outline-none focus:border-[#D40078] focus:ring-2 focus:ring-[#D40078]/10 transition-all resize-none bg-gray-50/50"
                  placeholder="اكتب تفاصيل الإشعار هنا..."
                  value={notifForm.body}
                  onChange={(e) =>
                    setNotifForm((p) => ({ ...p, body: e.target.value }))
                  }
                />
              </div>

              {/* Attachments Section */}
              <div className="space-y-3">
                <div className="flex items-center justify-between">
                  <label className="text-sm font-semibold text-gray-700">
                    المرفقات (اختياري)
                  </label>
                  <span className="text-[11px] text-gray-400">
                    الحد الأقصى: 5 ملفات
                  </span>
                </div>

                <div className="flex flex-wrap gap-2">
                  {notifForm.attachments.map((file, idx) => (
                    <div
                      key={idx}
                      className="flex items-center gap-2 bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-1.5"
                    >
                      <span className="text-xs text-gray-600 truncate max-w-[120px]">
                        {file.name}
                      </span>
                      <button
                        onClick={() => removeAttachment(idx)}
                        className="text-gray-400 hover:text-red-500 transition-colors"
                      >
                        <Trash2 className="w-3.5 h-3.5" />
                      </button>
                    </div>
                  ))}

                  {notifForm.attachments.length < 5 && (
                    <label className="flex items-center justify-center gap-2 border-2 border-dashed border-gray-200 rounded-lg px-4 py-2 cursor-pointer hover:border-[#D40078] hover:bg-[#D40078]/5 transition-all">
                      <input
                        type="file"
                        multiple
                        className="hidden"
                        onChange={handleFileChange}
                      />
                      <Paperclip className="w-4 h-4 text-gray-400" />
                      <span className="text-xs text-gray-500 font-medium">
                        إضافة ملف
                      </span>
                    </label>
                  )}
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Footer */}
        <div className="p-6 border-t border-gray-100 bg-gray-50/50 flex justify-end">
          <GradientButton
            onClick={type === "sms" ? handleSendSms : handleSendNotif}
            disabled={sendingSms || sendingNotif}
            className="px-8 py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg"
            leftIcon={
              !(sendingSms || sendingNotif) ? (
                <Send className="w-5 h-5" />
              ) : null
            }
          >
            {sendingSms || sendingNotif ? (
              <span className="flex items-center gap-2">
                <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                جارٍ الإرسال...
              </span>
            ) : type === "sms" ? (
              "إرسال SMS الآن"
            ) : (
              "إرسال الإشعار الآن"
            )}
          </GradientButton>
        </div>
      </div>
      <div className="flex-1" onClick={onClose} />
    </div>
  );
}
