"use client";

import { useEffect, useMemo, useState } from "react";
import { X, Send } from "lucide-react";
import { notify } from "@/lib/helpers/toastify";

import Stepper from "@/components/common/Stepper";
import FormInput from "@/components/common/InputField";
import StepButtonsSmart from "@/components/common/StepButtonsSmart";
import SearchableSelect from "@/components/common/SearchableSelect";
import DatePickerSmart from "@/components/common/DatePickerSmart";

import { useGetInstituteBranchesQuery } from "@/store/services/instituteBranchesApi";
import { useGetStudentContactsSummaryQuery } from "@/store/services/contactsApi";
import { useGetMessageTemplatesQuery } from "@/store/services/messageTemplatesApi";
import { useSendSingleSmsMutation } from "@/store/services/messagesApi";
import { useCreateNotificationMutation } from "@/store/services/notificationsApi";
import { useGetStudentPaymentsSummaryQuery } from "@/store/services/studentPaymentsApi";
import GradientButton from "@/components/common/GradientButton";
import { debugLogger } from "@/lib/helpers/debugLogger";

function toNumOrNull(v) {
  if (v === "" || v === null || v === undefined) return null;
  const n = Number(v);
  return Number.isNaN(n) ? null : n;
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

export default function PaymentAddModal({
  open,
  title = "إضافة دفعة",
  loading = false,
  onClose,
  onSubmit,
  students = [],
  defaultInstituteBranchId = "",
  initialData = null,
  showReason = false,
}) {
  const isEdit = !!initialData;
  // const total = isEdit ? 1 : 2;
  const total = 2;

  const safeStudents = useMemo(() => {
    if (Array.isArray(students)) return students;
    if (Array.isArray(students?.data)) return students.data;
    return [];
  }, [students]);

  const { data: branchesRes, isLoading: loadingBranches } =
    useGetInstituteBranchesQuery();

  const branches = useMemo(() => {
    const arr = branchesRes?.data;
    return Array.isArray(arr) ? arr : [];
  }, [branchesRes]);

  const branchOptions = useMemo(() => {
    return branches
      .filter((b) => b && b.id != null)
      .map((b) => ({
        value: String(b.id),
        label: String(b.name ?? "").trim(),
      }))
      .filter((o) => o.label.length > 0);
  }, [branches]);

  const currencyOptions = useMemo(
    () => [
      { value: "USD", label: "USD" },
      { value: "SYP", label: "SYP" },
    ],
    [],
  );

  const emptyForm = useMemo(
    () => ({
      receipt_number: "",
      institute_branch_id: defaultInstituteBranchId
        ? String(defaultInstituteBranchId)
        : "",
      student_id: "",
      currency: "USD",
      amount_usd: "",
      amount_syp: "",
      exchange_rate_at_payment: "",
      paid_date: "",
      description: "",
      reason: "",
    }),
    [defaultInstituteBranchId],
  );

  const [form, setForm] = useState(emptyForm);
  const [step, setStep] = useState(1);

  const [savedPayment, setSavedPayment] = useState(null);

  const [messageForm, setMessageForm] = useState({
    type: "sms",
    template_id: "",
    phone: "",
    message: "",
    title: "", // For notification
    note: "",
    lang: 0,
  });

  const { data: templatesRes } = useGetMessageTemplatesQuery(undefined, {
    skip: !open,
  });

  const templates = Array.isArray(templatesRes?.data) ? templatesRes.data : [];

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
      skip: !open || !form.student_id || step !== 2,
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

  const { data: studentSummary, isFetching: loadingSummary } =
    useGetStudentPaymentsSummaryQuery(form.student_id, {
      skip: !open || !form.student_id || step !== 2,
    });

  const contract = useMemo(() => {
    if (!studentSummary) return null;
    return (
      studentSummary?.enrollment_contract ||
      studentSummary?.contracts_summary?.[0] ||
      null
    );
  }, [studentSummary]);

  // const [sendSingleMessage, { isLoading: sendingMessage }] =
  //   useSendSingleMessageMutation();
  const [sendSingleSms, { isLoading: sendingSms }] = useSendSingleSmsMutation();
  const [createNotif, { isLoading: sendingNotif }] =
    useCreateNotificationMutation();

  const sendingMessage = sendingSms || sendingNotif;

  useEffect(() => {
    if (!open) return;

    setStep(1);
    setSavedPayment(null);

    setMessageForm({
      type: "sms",
      template_id: "",
      phone: "",
      message: "",
      title: "وصل استلام جديد",
      note: "",
      lang: 0,
    });
  }, [open]);

  useEffect(() => {
    if (!open || step !== 1) return;

    if (initialData) {
      setForm({
        receipt_number: initialData.receipt_number ?? "",
        institute_branch_id: String(initialData.institute_branch_id ?? ""),
        student_id: String(initialData.student_id ?? ""),
        currency: initialData.currency ?? "USD",
        amount_usd: initialData.amount_usd ?? "",
        amount_syp: initialData.amount_syp ?? "",
        exchange_rate_at_payment: initialData.exchange_rate_at_payment ?? "",
        paid_date: initialData.paid_date ?? "",
        description: initialData.description ?? "",
        reason: "",
      });
    } else {
      setForm(emptyForm);
    }
  }, [open, step, initialData, emptyForm]);

  const filteredStudents = useMemo(() => {
    const bid = String(form.institute_branch_id || "");
    if (!bid) return [];
    return safeStudents.filter((s) => {
      const sid1 = s?.institute_branch_id;
      const sid2 = s?.institute_branch?.id;
      return String(sid1 ?? sid2 ?? "") === bid;
    });
  }, [safeStudents, form.institute_branch_id]);

  const studentOptions = useMemo(() => {
    return filteredStudents
      .filter((s) => s && s.id != null)
      .map((s) => ({
        value: String(s.id),
        label: String(
          s.full_name ??
            s.fullName ??
            `${s.first_name ?? ""} ${s.last_name ?? ""}`.trim() ??
            `طالب #${s.id}`,
        ).trim(),
      }))
      .filter((o) => o.label.length > 0);
  }, [filteredStudents]);

  const selectedStudent = useMemo(() => {
    return safeStudents.find((s) => String(s?.id) === String(form.student_id));
  }, [safeStudents, form.student_id]);

  useEffect(() => {
    if (!open) return;
    if (!form.student_id) return;

    const bid = String(form.institute_branch_id || "");
    if (!bid) {
      setForm((p) => ({ ...p, student_id: "" }));
      return;
    }

    const current = safeStudents.find(
      (s) => String(s?.id) === String(form.student_id),
    );
    if (!current) {
      setForm((p) => ({ ...p, student_id: "" }));
      return;
    }

    const currentBid = String(
      current?.institute_branch_id ?? current?.institute_branch?.id ?? "",
    );

    if (currentBid && currentBid !== bid) {
      setForm((p) => ({ ...p, student_id: "" }));
    }
  }, [open, form.institute_branch_id, form.student_id, safeStudents]);

  useEffect(() => {
    if (!open) return;

    setForm((p) => {
      if (p.currency === "USD") {
        return { ...p, amount_syp: "", exchange_rate_at_payment: "" };
      }
      return { ...p, amount_usd: "" };
    });
  }, [open, form.currency]);

  useEffect(() => {
    if (step !== 2) return;
    if (smsContactOptions.length > 0 && !messageForm.phone) {
      setMessageForm((p) => ({ ...p, phone: smsContactOptions[0].value }));
    }
  }, [smsContactOptions, step]);

  useEffect(() => {
    if (!open || step !== 2) return;

    const amount =
      form.currency === "USD"
        ? `${form.amount_usd || 0}$`
        : `${form.amount_syp || 0} ل.س`;

    const studentName =
      selectedStudent?.full_name ||
      selectedStudent?.fullName ||
      `${selectedStudent?.first_name || ""} ${selectedStudent?.last_name || ""}`.trim() ||
      "";

    const text = `تم استلام دفعة من الطالب ${studentName} بقيمة ${amount} بتاريخ ${form.paid_date || ""} - رقم الإيصال: ${form.receipt_number || ""}`;

    setMessageForm((prev) => ({ ...prev, message: text }));
  }, [
    open,
    step,
    form.currency,
    form.amount_usd,
    form.amount_syp,
    form.paid_date,
    form.receipt_number,
    selectedStudent,
  ]);

  useEffect(() => {
    if (messageForm.type !== "sms") return;
    if (!messageForm.template_id) return;

    const selectedTemplate = templates.find(
      (t) => String(t.id) === String(messageForm.template_id),
    );

    if (!selectedTemplate) return;

    let body = selectedTemplate.body || "";

    const amount =
      form.currency === "USD"
        ? `${form.amount_usd || 0}$`
        : `${form.amount_syp || 0} ل.س`;

    const studentName =
      selectedStudent?.full_name ||
      selectedStudent?.fullName ||
      `${selectedStudent?.first_name || ""} ${selectedStudent?.last_name || ""}`.trim() ||
      "";

    body = body
      .replaceAll("{student_name}", studentName)
      .replaceAll("{amount}", amount)
      .replaceAll("{paid_date}", form.paid_date || "")
      .replaceAll("{receipt_number}", form.receipt_number || "");

    setMessageForm((prev) => ({
      ...prev,
      message: body,
    }));
  }, [
    messageForm.template_id,
    messageForm.type,
    templates,
    form.currency,
    form.amount_usd,
    form.amount_syp,
    form.paid_date,
    form.receipt_number,
    selectedStudent,
  ]);

  const validateStep1 = () => {
    if (!form.receipt_number.trim()) return "رقم الإيصال مطلوب";
    if (!form.institute_branch_id) return "يرجى اختيار فرع المعهد";
    if (!form.student_id) return "يرجى اختيار الطالب";
    if (!form.paid_date) return "يرجى إدخال تاريخ الدفع";
    if (!form.currency) return "يرجى اختيار العملة";

    if (form.currency === "USD") {
      if (!form.amount_usd) return "يرجى إدخال مبلغ USD";
    } else {
      if (!form.amount_syp) return "يرجى إدخال مبلغ ل.س";
      if (!form.exchange_rate_at_payment) return "يرجى إدخال سعر الصرف";
    }

    if (showReason && initialData && !form.reason.trim())
      return "يرجى إدخال سبب التعديل";

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

  const buildPaymentPayload = () => {
    const isUSD = form.currency === "USD";

    return {
      receipt_number: form.receipt_number || null,
      institute_branch_id: toNumOrNull(form.institute_branch_id),
      student_id: toNumOrNull(form.student_id),
      amount_usd: isUSD ? toNumOrNull(form.amount_usd) : null,
      amount_syp: !isUSD ? toNumOrNull(form.amount_syp) : null,
      exchange_rate_at_payment: !isUSD
        ? toNumOrNull(form.exchange_rate_at_payment)
        : null,
      currency: form.currency || "USD",
      paid_date: form.paid_date || null,
      description: form.description || null,
      ...(showReason ? { reason: form.reason || null } : {}),
    };
  };

  const handleNextFromStep1 = async () => {
    const err = validateStep1();
    if (err) {
      notify.error(err, "خطأ");
      return;
    }

    const payload = buildPaymentPayload();

    try {
      const saved = await onSubmit?.(payload);
      setSavedPayment(saved || payload);
      setStep(2);
    } catch (e) {
      console.error(e);
      debugLogger.error(e, "Payment Submission");
      notify.error(e?.data?.message || e?.message || "فشل في حفظ الدفعة");
    }
  };

  const handleSkipMessages = () => {
    notify.success(
      isEdit ? "تم إرسال طلب التعديل بنجاح" : "تم حفظ الدفعة بنجاح",
    );
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
        debugLogger.error(err, "SMS Sending");
        notify.error(
          err?.data?.message || err?.message || "فشل في إرسال الرسالة",
        );
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
        debugLogger.error(err, "Notification Sending");
        notify.error(err?.data?.message || "فشل في إرسال الإشعار");
      }
    }
  };
  if (!open) return null;

  return (
    <div className="fixed inset-0 bg-black/40 z-50 flex justify-start backdrop-blur-md">
      <div
        dir="rtl"
        className="w-full sm:w-[560px] bg-white h-full shadow-xl flex flex-col"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h2 className="text-[#6F013F] font-semibold text-lg">
            {initialData ? "تعديل دفعة" : title}
          </h2>
          <button
            onClick={onClose}
            type="button"
            className="text-gray-400 hover:text-gray-700"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        <div className="flex-1 overflow-y-auto px-6 py-4">
          <Stepper current={step} total={total} />

          {step === 1 && (
            <div className="mt-6 space-y-4">
              <FormInput
                label="رقم الإيصال"
                placeholder="REC-001"
                value={form.receipt_number}
                onChange={(e) =>
                  setForm((p) => ({ ...p, receipt_number: e.target.value }))
                }
              />

              <SearchableSelect
                label="فرع المعهد"
                required
                value={form.institute_branch_id}
                onChange={(v) =>
                  setForm((p) => ({ ...p, institute_branch_id: v }))
                }
                options={branchOptions}
                placeholder={
                  loadingBranches ? "جارٍ التحميل..." : "اختر الفرع..."
                }
                disabled={loadingBranches}
                allowClear
              />

              <SearchableSelect
                label="الطالب"
                required
                value={form.student_id}
                onChange={(v) => setForm((p) => ({ ...p, student_id: v }))}
                options={studentOptions}
                placeholder={
                  form.institute_branch_id
                    ? studentOptions.length
                      ? "اختر الطالب..."
                      : "لا يوجد طلاب بهذا الفرع"
                    : "اختر الفرع أولاً"
                }
                disabled={!form.institute_branch_id}
                allowClear
              />

              <SearchableSelect
                label="العملة"
                required
                value={form.currency}
                onChange={(v) => setForm((p) => ({ ...p, currency: v }))}
                options={currencyOptions}
                placeholder="اختر العملة..."
                allowClear
              />

              <DatePickerSmart
                label="تاريخ الدفع"
                required
                value={form.paid_date}
                onChange={(iso) => setForm((p) => ({ ...p, paid_date: iso }))}
                placeholder="dd/mm/yyyy"
              />

              {form.currency === "USD" && (
                <FormInput
                  label="المبلغ بالدولار"
                  required
                  placeholder="100"
                  value={form.amount_usd}
                  onChange={(e) =>
                    setForm((p) => ({ ...p, amount_usd: e.target.value }))
                  }
                />
              )}

              {form.currency === "SYP" && (
                <>
                  <FormInput
                    label="المبلغ بالليرة"
                    required
                    placeholder="1000000"
                    value={form.amount_syp}
                    onChange={(e) =>
                      setForm((p) => ({ ...p, amount_syp: e.target.value }))
                    }
                  />

                  <FormInput
                    label="سعر الصرف"
                    required
                    placeholder="10000"
                    value={form.exchange_rate_at_payment}
                    onChange={(e) =>
                      setForm((p) => ({
                        ...p,
                        exchange_rate_at_payment: e.target.value,
                      }))
                    }
                  />
                </>
              )}

              <FormInput
                label="الوصف"
                placeholder="دفعة نقدًا..."
                value={form.description}
                onChange={(e) =>
                  setForm((p) => ({ ...p, description: e.target.value }))
                }
              />

              {showReason && initialData && (
                <FormInput
                  label="سبب التعديل"
                  required
                  placeholder="سبب تعديل الدفعة..."
                  value={form.reason}
                  onChange={(e) =>
                    setForm((p) => ({ ...p, reason: e.target.value }))
                  }
                />
              )}
            </div>
          )}

          {step === 2 && (
            <div className="mt-6 space-y-5">
              <div className="grid grid-cols-2 gap-3 text-sm text-gray-600">
                <div>
                  اسم الطالب:{" "}
                  <span className="text-gray-800 font-medium">
                    {selectedStudent?.full_name ||
                      selectedStudent?.fullName ||
                      `${selectedStudent?.first_name || ""} ${selectedStudent?.last_name || ""}`.trim() ||
                      "-"}
                  </span>
                </div>

                <div>
                  المبلغ المتبقي:{" "}
                  <span className="text-gray-800 font-medium font-mono">
                    {loadingSummary ? (
                      "..."
                    ) : (
                      <>{Math.round(contract?.remaining_amount_usd ?? 0)}$</>
                    )}
                  </span>
                </div>

                <div>
                  المبلغ الكلي:{" "}
                  <span className="text-gray-800 font-medium font-mono">
                    {loadingSummary ? (
                      "..."
                    ) : (
                      <>{Math.round(contract?.total_amount_usd ?? 0)}$</>
                    )}
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
                    placeholder="مثلاً: تنبيه بخصوص الدفعة"
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

        <div className="px-6 py-4 bg-white border-t border-gray-100">
          {step === 1 ? (
            <StepButtonsSmart
              step={step}
              total={total}
              isEdit={!!initialData}
              loading={loading}
              onNext={handleNextFromStep1}
              onBack={onClose}
              nextLabel={initialData ? "إرسال طلب تعديل" : "التالي"}
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
