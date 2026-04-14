"use client";

import { useEffect, useMemo, useState } from "react";
import { X, Send } from "lucide-react";
import { notify } from "@/lib/helpers/toastify";

import Stepper from "@/components/common/Stepper";
import StepButtonsSmart from "@/components/common/StepButtonsSmart";
import SearchableSelect from "@/components/common/SearchableSelect";
import FormInput from "@/components/common/InputField";
import GradientButton from "@/components/common/GradientButton";

// APIs
import { useGetStudentsDetailsQuery } from "@/store/services/studentsApi";
import {
  useAddManualAttendanceMutation,
  useUpdateAttendanceMutation,
} from "@/store/services/attendanceApi";

import { useGetStudentContactsSummaryQuery } from "@/store/services/contactsApi";
import { useGetMessageTemplatesQuery } from "@/store/services/messageTemplatesApi";
import { useSendSingleSmsMutation } from "@/store/services/messagesApi";
import { useCreateNotificationMutation } from "@/store/services/notificationsApi";

function studentFullName(s) {
  if (!s) return "";
  if (s.full_name) return s.full_name;
  const first = s.first_name || s.name || "";
  const last = s.last_name || s.family_name || s.surname || "";
  return `${first} ${last}`.trim();
}

function getSmsContactFromSummary(summaryData) {
  if (!summaryData) return null;

  const data = summaryData?.data || {};

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

  const smsOnly = allContacts.filter(
    (c) =>
      c && c.type === "phone" && c.supports_sms === true && c.full_phone_number,
  );

  const primary = smsOnly.find((c) => c.is_primary);
  return primary || smsOnly[0] || null;
}

function statusLabelArabic(status) {
  if (status === "present") return "حاضر";
  if (status === "late") return "متأخر";
  if (status === "absent") return "غائب";
  return status || "-";
}

function todayYMD() {
  return new Date().toLocaleDateString("en-CA");
}

export default function AddAttendanceModal({ isOpen, onClose, record }) {
  const isEdit = !!record;
  const total = 2;

  const [addManualAttendance] = useAddManualAttendanceMutation();
  const [updateAttendance] = useUpdateAttendanceMutation();
  const [sendSingleSms, { isLoading: sendingSms }] = useSendSingleSmsMutation();
  const [createNotif, { isLoading: sendingNotif }] =
    useCreateNotificationMutation();

  const sendingMessage = sendingSms || sendingNotif;

  const { data: studentsRes } = useGetStudentsDetailsQuery(undefined, {
    skip: !isOpen,
  });
  const allStudents = studentsRes?.data || [];

  const { data: templatesRes } = useGetMessageTemplatesQuery(undefined, {
    skip: !isOpen,
  });
  const templates = Array.isArray(templatesRes?.data) ? templatesRes.data : [];

  const [loading, setLoading] = useState(false);
  const [step, setStep] = useState(1);

  const initialForm = {
    student_id: "",
    status: "",
  };

  const [form, setForm] = useState(initialForm);

  const [savedAttendance, setSavedAttendance] = useState(null);

  const [messageForm, setMessageForm] = useState({
    type: "sms",
    template_id: "",
    phone: "",
    message: "",
    title: "", // For notification
    note: "",
    lang: 0,
  });

  const selectedStudent = useMemo(() => {
    return allStudents.find((s) => String(s.id) === String(form.student_id));
  }, [allStudents, form.student_id]);

  const studentOptions = useMemo(() => {
    return (allStudents || [])
      .map((s) => {
        const label = studentFullName(s);
        if (!label) return null;
        return { value: String(s.id), label };
      })
      .filter(Boolean);
  }, [allStudents]);

  const statusOptions = [
    { value: "present", label: "حاضر" },
    { value: "late", label: "متأخر" },
    { value: "absent", label: "غائب" },
  ];

  const smsTemplates = useMemo(() => {
    return templates
      .filter((t) => t?.type === "sms" && !!t?.is_active)
      .map((t) => ({
        value: String(t.id),
        label: t.name,
      }));
  }, [templates]);

  const { data: contactsSummary, isFetching: loadingContacts } =
    useGetStudentContactsSummaryQuery(form.student_id, {
      skip: !isOpen || !form.student_id || step !== 2,
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

    return allContacts
      .filter(
        (c) => c && c.type === "phone" && c.supports_sms && c.full_phone_number,
      )
      .map((c) => ({
        value: c.full_phone_number,
        label: `${c.owner_type === "father" ? "الأب" : c.owner_type === "mother" ? "الأم" : "الطالب"}: ${c.full_phone_number}`,
      }));
  }, [contactsSummary]);

  useEffect(() => {
    if (!isOpen) return;

    setStep(1);
    setSavedAttendance(null);
    setMessageForm({
      type: "sms",
      template_id: "",
      phone: "",
      message: "",
      title: "تنبيه حضور/غياب",
      note: "",
      lang: 0,
    });

    if (record) {
      setForm({
        student_id: record?.student_id ? String(record.student_id) : "",
        status: record?.status || "",
      });
    } else {
      setForm(initialForm);
    }
  }, [isOpen, record]);

  useEffect(() => {
    if (step !== 2) return;
    if (smsContactOptions.length > 0 && !messageForm.phone) {
      setMessageForm((p) => ({ ...p, phone: smsContactOptions[0].value }));
    }
  }, [smsContactOptions, step]);

  useEffect(() => {
    if (!isOpen || step !== 2) return;

    const studentName = studentFullName(selectedStudent);
    const attendanceDate =
      savedAttendance?.attendance_date || record?.attendance_date || todayYMD();
    const statusLabel = statusLabelArabic(form.status);

    const text = `نحيطكم علماً بأن الطالب ${studentName} كانت حالته اليوم (${statusLabel}) بتاريخ ${attendanceDate}.`;

    setMessageForm((prev) => ({ ...prev, message: text }));
  }, [
    isOpen,
    step,
    selectedStudent,
    form.status,
    savedAttendance,
    record,
  ]);

  useEffect(() => {
    if (messageForm.type !== "sms") return;
    if (!messageForm.template_id) return;

    const selectedTemplate = templates.find(
      (t) => String(t.id) === String(messageForm.template_id),
    );

    if (!selectedTemplate) return;

    const attendanceDate =
      savedAttendance?.attendance_date || record?.attendance_date || todayYMD();

    let body = selectedTemplate.body || "";

    body = body
      .replaceAll("{student_name}", studentFullName(selectedStudent))
      .replaceAll("{status}", statusLabelArabic(form.status))
      .replaceAll("{attendance_status}", statusLabelArabic(form.status))
      .replaceAll("{attendance_date}", attendanceDate);

    setMessageForm((prev) => ({
      ...prev,
      message: body,
    }));
  }, [
    messageForm.template_id,
    messageForm.type,
    templates,
    selectedStudent,
    form.status,
    savedAttendance,
    record,
  ]);

  const validateStep1 = () => {
    if (!form.student_id) return "يرجى اختيار الطالب";
    if (!form.status) return "يرجى اختيار الحالة";
    return null;
  };

  const validateMessageStep = () => {
    if (messageForm.type === "sms") {
      if (!messageForm.phone) return "يرجى اختيار رقم هاتف";
      if (!messageForm.message.trim()) return "نص الرسالة مطلوب";
    } else if (messageForm.type === "notification") {
      if (!messageForm.title.trim()) return "عنوان الإشعار مطلوب";
      if (messageForm.title.trim().length < 3)
        return "العنوان يجب أن لا يقل عن 3 أحرف";
      if (!messageForm.message.trim()) return "محتوى الإشعار مطلوب";
      if (messageForm.message.trim().length < 5)
        return "المحتوى يجب أن لا يقل عن 5 أحرف";
    }
    return null;
  };

  const handleNextFromStep1 = async () => {
    const err = validateStep1();
    if (err) {
      notify.error(err);
      return;
    }

    try {
      setLoading(true);

      let saved;

      if (!isEdit) {
        saved = await addManualAttendance({
          student_id: Number(form.student_id),
          status: form.status,
        }).unwrap();
      } else {
        saved = await updateAttendance({
          id: record.id,
          institute_branch_id: record?.institute_branch_id,
          batch_id: record?.batch_id,
          attendance_date: record?.attendance_date,
          student_id: Number(form.student_id),
          status: form.status,
        }).unwrap();
      }

      setSavedAttendance(saved?.data || saved || null);
      setStep(2);
    } catch (err) {
      notify.error(err?.data?.message || "حدث خطأ أثناء الحفظ");
    } finally {
      setLoading(false);
    }
  };

  const handleSkipMessages = () => {
    notify.success(isEdit ? "تم تعديل السجل بنجاح" : "تم تسجيل الحضور بنجاح");
    onClose?.();
  };

  const handleSendMessage = async () => {
    const err = validateMessageStep();
    if (err) {
      notify.error(err);
      return;
    }

    if (messageForm.type === "sms") {
      try {
        await sendSingleSms({
          phone: messageForm.phone,
          message: messageForm.message,
          lang: messageForm.lang ?? 0,
        }).unwrap();

        notify.success("تم إرسال الرسالة بنجاح");
        onClose?.();
      } catch (err) {
        console.error(err);
        notify.error(err?.data?.message || "فشل في إرسال الرسالة");
      }
    } else if (messageForm.type === "notification") {
      const userId = selectedStudent?.user_id || selectedStudent?.family?.user_id;

      if (!userId) {
        return notify.error(
          "هذا الطالب/العائلة لا يملك حساباً مسجلاً لتلقي الإشعارات",
        );
      }

      const payload = {
        title: messageForm.title,
        body: messageForm.message,
        target_snapshot: {
          type: "custom",
          user_ids: [Number(userId)],
        },
      };

      try {
        await createNotif(payload).unwrap();
        notify.success("تم إرسال الإشعار بنجاح");
        onClose?.();
      } catch (err) {
        console.error("❌ Notification Sending Error:", err);
        notify.error(err?.data?.message || "فشل في إرسال الإشعار");
      }
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/40 z-50 flex justify-start backdrop-blur-sm">
      <div
        className="w-full sm:w-[520px] bg-white h-full shadow-xl flex flex-col"
        dir="rtl"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h2 className="text-[#6F013F] font-semibold text-lg">
            {isEdit ? "تعديل حضور/غياب" : "تسجيل حضور/غياب"}
          </h2>
          <button onClick={onClose} type="button">
            <X className="w-5 h-5 text-gray-500" />
          </button>
        </div>

        {/* Body */}
        <div className="flex-1 overflow-y-auto px-6 py-4">
          <Stepper current={step} total={total} />

          {step === 1 && (
            <div className="mt-6 space-y-5">
              <SearchableSelect
                label="اسم الطالب"
                required
                value={form.student_id}
                onChange={(v) => setForm((p) => ({ ...p, student_id: v }))}
                options={studentOptions}
                placeholder="اكتب اسم الطالب..."
              />

              <SearchableSelect
                label="الحالة"
                required
                value={form.status}
                onChange={(v) => setForm((p) => ({ ...p, status: v }))}
                options={statusOptions}
                placeholder="اختر الحالة..."
              />
            </div>
          )}

          {step === 2 && (
            <div className="mt-6 space-y-5">
              <div className="grid grid-cols-1 gap-3 text-sm text-gray-600">
                <div>
                  اسم الطالب:{" "}
                  <span className="text-gray-800 font-medium">
                    {studentFullName(selectedStudent) || "-"}
                  </span>
                </div>

                <div>
                  الحالة:{" "}
                  <span className="text-gray-800 font-medium">
                    {statusLabelArabic(form.status)}
                  </span>
                </div>

                <div>
                  التاريخ:{" "}
                  <span className="text-gray-800 font-medium">
                    {savedAttendance?.attendance_date ||
                      record?.attendance_date ||
                      todayYMD()}
                  </span>
                </div>
              </div>

              <div className="flex items-center gap-8 py-2 border-b border-gray-50">
                <label className="flex items-center gap-2.5 cursor-pointer group">
                  <div className="relative flex items-center justify-center">
                    <input
                      type="radio"
                      name="msgType"
                      className="sr-only"
                      checked={messageForm.type === "sms"}
                      onChange={() =>
                        setMessageForm((p) => ({ ...p, type: "sms" }))
                      }
                    />
                    <div
                      className={`w-5 h-5 rounded-full border-2 transition-all flex items-center justify-center ${messageForm.type === "sms" ? "border-[#6F013F]" : "border-gray-300 group-hover:border-gray-400"}`}
                    >
                      {messageForm.type === "sms" && (
                        <div className="w-2.5 h-2.5 rounded-full bg-[#6F013F] animate-in zoom-in-50 duration-200" />
                      )}
                    </div>
                  </div>
                  <span
                    className={`text-sm font-bold transition-colors ${messageForm.type === "sms" ? "text-gray-900" : "text-gray-500 group-hover:text-gray-700"}`}
                  >
                    رسالة SMS
                  </span>
                </label>

                <label className="flex items-center gap-2.5 cursor-pointer group">
                  <div className="relative flex items-center justify-center">
                    <input
                      type="radio"
                      name="msgType"
                      className="sr-only"
                      checked={messageForm.type === "notification"}
                      onChange={() =>
                        setMessageForm((p) => ({ ...p, type: "notification" }))
                      }
                    />
                    <div
                      className={`w-5 h-5 rounded-full border-2 transition-all flex items-center justify-center ${messageForm.type === "notification" ? "border-[#6F013F]" : "border-gray-300 group-hover:border-gray-400"}`}
                    >
                      {messageForm.type === "notification" && (
                        <div className="w-2.5 h-2.5 rounded-full bg-[#6F013F] animate-in zoom-in-50 duration-200" />
                      )}
                    </div>
                  </div>
                  <span
                    className={`text-sm font-bold transition-colors ${messageForm.type === "notification" ? "text-gray-900" : "text-gray-500 group-hover:text-gray-700"}`}
                  >
                    إشعار (App)
                  </span>
                </label>
              </div>

              {messageForm.type === "sms" ? (
                <div className="space-y-4">
                  <SearchableSelect
                    label="نموذج الرسالة"
                    value={messageForm.template_id}
                    onChange={(v) =>
                      setMessageForm((p) => ({ ...p, template_id: v }))
                    }
                    options={smsTemplates}
                    placeholder="اختر نموذج رسالة..."
                  />

                  <SearchableSelect
                    label="رقم الهاتف (يدعم SMS)"
                    placeholder={
                      loadingContacts
                        ? "جارٍ تحميل الجهات..."
                        : smsContactOptions.length
                          ? "اختر الرقم..."
                          : "لا توجد أرقام تدعم SMS"
                    }
                    options={smsContactOptions}
                    value={messageForm.phone}
                    onChange={(v) => setMessageForm((p) => ({ ...p, phone: v }))}
                    disabled={loadingContacts || smsContactOptions.length === 0}
                    required
                  />

                  <div className="space-y-2">
                    <label className="text-sm font-semibold text-gray-700">
                      نص الرسالة
                    </label>
                    <textarea
                      rows={6}
                      className="w-full border border-gray-200 rounded-2xl p-4 text-sm outline-none focus:border-[#D40078] focus:ring-2 focus:ring-[#D40078]/10 transition-all resize-none bg-gray-50/50"
                      placeholder="اكتب رسالتك النصية هنا..."
                      value={messageForm.message}
                      onChange={(e) =>
                        setMessageForm((p) => ({
                          ...p,
                          message: e.target.value,
                        }))
                      }
                    />
                  </div>
                </div>
              ) : (
                <div className="space-y-4">
                  <FormInput
                    label="عنوان الإشعار"
                    placeholder="مثلاً: تنبيه بخصوص الامتحان"
                    value={messageForm.title}
                    onChange={(e) =>
                      setMessageForm((p) => ({ ...p, title: e.target.value }))
                    }
                    required
                  />

                  <div className="space-y-2">
                    <label className="text-sm font-semibold text-gray-700">
                      محتوى الإشعار
                    </label>
                    <textarea
                      rows={6}
                      className="w-full border border-gray-200 rounded-2xl p-4 text-sm outline-none focus:border-[#D40078] focus:ring-2 focus:ring-[#D40078]/10 transition-all resize-none bg-gray-50/50"
                      placeholder="اكتب تفاصيل الإشعار هنا..."
                      value={messageForm.message}
                      onChange={(e) =>
                        setMessageForm((p) => ({
                          ...p,
                          message: e.target.value,
                        }))
                      }
                    />
                  </div>
                </div>
              )}
            </div>
          )}
        </div>

        {/* Footer */}
        <div className="px-6 py-4 bg-white border-t border-gray-100">
          {step === 1 ? (
            <StepButtonsSmart
              step={step}
              total={total}
              isEdit={isEdit}
              loading={loading}
              onNext={handleNextFromStep1}
              onBack={onClose}
              nextLabel="التالي"
            />
          ) : (
            <div className="flex items-center justify-between gap-3">
              <button
                type="button"
                onClick={() => setStep(1)}
                className="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50"
              >
                رجوع
              </button>

              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={handleSkipMessages}
                  className="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                >
                  تخطي
                </button>

                <GradientButton
                  onClick={handleSendMessage}
                  disabled={sendingMessage}
                  className="px-6 py-2 flex items-center gap-2"
                  leftIcon={!sendingMessage ? <Send className="w-4 h-4" /> : null}
                >
                  {sendingMessage ? (
                    <span className="flex items-center gap-2">
                      <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                      جارٍ الإرسال...
                    </span>
                  ) : messageForm.type === "sms" ? (
                    "إرسال SMS"
                  ) : (
                    "إرسال إشعار"
                  )}
                </GradientButton>
              </div>
            </div>
          )}
        </div>
      </div>

      <div className="flex-1" onClick={onClose} />
    </div>
  );
}
