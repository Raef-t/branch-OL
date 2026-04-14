"use client";

export default function PaymentsSummary({ data }) {
  if (!data) return null;

  const payload = data?.data || data;
  const summary = payload?.enrollment_contract || payload?.contracts_summary?.[0];

  const lastReceipt = payload?.payments?.length
    ? (payload.payments[payload.payments.length - 1].receipt_number || payload.payments[payload.payments.length - 1].receiptNumber)
    : "—";

  const items = [
    {
      label: "اسم الطالب:",
      value: payload?.full_name || payload?.student_name || "—",
    },
    {
      label: "رقم الإيصال:",
      value: lastReceipt || "—",
    },
    {
      label: "الدورة:",
      value: payload?.current_batch?.name || "—",
    },
    {
      label: "المبلغ الكلي:",
      value: summary ? `${summary.total_amount_usd}$` : "—",
    },
    {
      label: "المبلغ المدفوع كلياً:",
      value: summary ? `${summary.paid_amount_usd}$` : "—",
    },
    {
      label: "المبلغ المتبقي:",
      value: summary ? `${summary.remaining_amount_usd}$` : "—",
    },
    {
      label: "نسبة الحسم:",
      value: summary ? `%${summary.discount_percentage}` : "—",
    },
    {
      label: "قيمة الخصم:",
      value: summary ? `${summary.discount_amount}$` : "—",
    },
  ];

  return (
    <div className="bg-white border border-gray-200 rounded-2xl p-4 md:p-6">
      <div className="grid grid-cols-1 md:grid-cols-3 gap-y-6 gap-x-8 text-right">
        {items.map((item, index) => (
          <div key={index} className="flex items-center gap-2">
            <span className="text-[14px] text-gray-500">{item.label}</span>
            <span className="text-[15px] font-semibold text-gray-800">
              {item.value}
            </span>
          </div>
        ))}
      </div>
    </div>
  );
}
