"use client";

import { useMemo, useState } from "react";
import BaseModal from "@/components/common/BaseModal";
import PrintButton from "@/components/common/PrintButton";
import ExcelButton from "@/components/common/ExcelButton";
import { useGetStudentContactsSummaryQuery } from "@/store/services/contactsApi";
import {
  Phone,
  MessageSquare,
  MessageCircle,
  User,
  Home,
  PhoneCall,
  MapPin,
  Bus,
  FileText,
  HeartPulse,
  Brain,
  GraduationCap,
} from "lucide-react";

import * as XLSX from "xlsx";
import { saveAs } from "file-saver";

/* ================= helpers ================= */
const g = (x) => (x === "male" ? "ذكر" : x === "female" ? "أنثى" : "—");
const safe = (v) => (v === null || v === undefined || v === "" ? "—" : v);

function normalizeContact(contact = {}) {
  return {
    ...contact,
    type: contact.type || "phone",
    owner_name: contact.owner_name || null,
    full_phone_number:
      contact.full_phone_number ||
      (contact.country_code && contact.phone_number
        ? `${contact.country_code}${contact.phone_number}`
        : contact.phone_number || contact.value || "—"),
    phone_number: contact.phone_number || contact.value || "—",
    supports_call: Boolean(contact.supports_call),
    supports_whatsapp: Boolean(contact.supports_whatsapp),
    supports_sms: Boolean(contact.supports_sms),
    is_primary: Boolean(contact.is_primary),
    notes: contact.notes || null,
    is_landline: Boolean(contact.is_landline),
  };
}

/* ================= component ================= */
export default function StudentDetailsModal({
  open,
  onClose,
  student,
  isLoading = false,
}) {
  const [activeTab, setActiveTab] = useState("info"); // "info" | "contacts"

  const { data: contactsSummary, isFetching: loadingContacts } =
    useGetStudentContactsSummaryQuery(student?.id, {
      skip: !open || !student?.id || activeTab !== "contacts",
    });

  const guardians = Array.isArray(student?.family?.guardians)
    ? student.family.guardians
    : [];

  /* ================= derived contacts fallback from student payload ================= */
  const fallbackContactsData = useMemo(() => {
    if (!student)
      return {
        personal_contacts: [],
        family_contacts: [],
        guardians_contacts: [],
      };

    const personal = Array.isArray(student.personal_contacts)
      ? student.personal_contacts.map(normalizeContact)
      : [];

    const family = Array.isArray(student.family?.family_contacts)
      ? student.family.family_contacts.map(normalizeContact)
      : [];

    const guardiansContacts = guardians.map((guardian) => ({
      relationship: guardian.relationship,
      name:
        guardian.name ||
        `${safe(guardian.first_name)} ${safe(guardian.last_name)}`.trim(),
      legacy_phone: guardian.legacy_phone || null,
      details: Array.isArray(guardian.contact_details)
        ? guardian.contact_details.map(normalizeContact)
        : [],
    }));

    return {
      personal_contacts: personal,
      family_contacts: family,
      guardians_contacts: guardiansContacts,
    };
  }, [student, guardians]);

  const showLoading = open && (isLoading || !student);

  if (showLoading) {
    return (
      <BaseModal open={open} onClose={onClose} title="تفاصيل الطالب">
        <div className="py-20 flex flex-col items-center justify-center gap-4">
          <div className="relative h-12 w-12">
            <div className="absolute inset-0 rounded-full border-4 border-gray-100" />
            <div className="absolute inset-0 rounded-full border-4 border-t-[#6F013F] animate-spin" />
          </div>
          <p className="text-sm font-medium text-gray-500 animate-pulse">
            جاري جلب ملف الطالب...
          </p>
        </div>
      </BaseModal>
    );
  }

  if (!student) {
    return (
      <BaseModal open={open} onClose={onClose} title="تفاصيل الطالب">
        <div className="text-gray-400 text-center py-10">لا يوجد بيانات</div>
      </BaseModal>
    );
  }

  const father = student.family?.guardians?.find(
    (x) => x.relationship === "father",
  );
  const mother = student.family?.guardians?.find(
    (x) => x.relationship === "mother",
  );

  /* ================= PRINT ================= */
  const handlePrint = () => {
    const guardiansHtml =
      guardians.length > 0
        ? guardians
            .map(
              (guardian) => `
                <div class="card">
                  <div class="label">الولي (${safe(guardian.relationship)})</div>
                  <div>${safe(guardian.name || `${guardian.first_name || ""} ${guardian.last_name || ""}`)}</div>
                  <div style="margin-top:6px;font-size:12px;color:#666;">
                    الرقم الوطني: ${safe(guardian.national_id)}
                  </div>
                  <div style="margin-top:6px;font-size:12px;color:#666;">
                    رقم قديم: ${safe(guardian.legacy_phone)}
                  </div>
                </div>
              `,
            )
            .join("")
        : `<div class="card">—</div>`;

    const html = `
      <html dir="rtl">
        <head>
          <title>بطاقة معلومات طالب العلماء</title>
          <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');
            
            body { 
              font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, Arial; 
              padding: 40px; 
              direction: rtl; 
              color: #333;
              background: #fff;
              line-height: 1.6;
            }
            
            .header {
              display: flex;
              justify-content: space-between;
              align-items: center;
              border-bottom: 3px solid #6F013F;
              padding-bottom: 20px;
              margin-bottom: 30px;
            }
            
            .title-box h1 {
              color: #6F013F;
              margin: 0;
              font-size: 28px;
              font-weight: bold;
            }
            
            .title-box p {
              margin: 5px 0 0;
              color: #666;
              font-size: 14px;
            }
            
            h2 { 
              color: #6F013F; 
              background: #FDF2F7;
              padding: 10px 15px;
              border-right: 5px solid #6F013F;
              font-size: 18px;
              margin: 30px 0 15px;
              border-radius: 4px;
            }
            
            .grid {
              display: grid;
              grid-template-columns: repeat(2, 1fr);
              gap: 15px;
            }
            
            .grid-3 {
              display: grid;
              grid-template-columns: repeat(3, 1fr);
              gap: 15px;
            }
            
            .card {
              border: 1px solid #f0f0f0;
              border-radius: 12px;
              padding: 15px;
              background: #fafafb;
            }
            
            .label {
              font-size: 11px;
              font-weight: bold;
              color: #888;
              text-transform: uppercase;
              margin-bottom: 5px;
              letter-spacing: 0.5px;
            }
            
            .value {
              font-size: 14px;
              color: #222;
              font-weight: 500;
            }
            
            .guardian-card {
              border: 1px solid #eef2f6;
              padding: 15px;
              border-radius: 15px;
              background: #fff;
              box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            }
            
            .footer {
              margin-top: 50px;
              text-align: center;
              font-size: 12px;
              color: #999;
              border-top: 1px solid #eee;
              padding-top: 20px;
            }
            
            @media print {
              body { padding: 20px; }
              .card { background: #fff !important; border: 1px solid #eee; }
            }
          </style>
        </head>
        <body>
          <div class="header">
            <div class="title-box">
              <h1>العلماء</h1>
              <p>بطاقة معلومات الطالب الرسمية</p>
            </div>
            <div style="text-align: left; font-size: 12px; color: #666;">
              تاريخ الاستخراج: ${new Date().toLocaleDateString("ar-EG")}
            </div>
          </div>

          <h2>المعلومات الشخصية</h2>
          <div class="grid">
            <div class="card"><div class="label">الاسم الثلاثي</div><div class="value">${safe(student.full_name)}</div></div>
            <div class="card"><div class="label">الجنس</div><div class="value">${g(student.gender)}</div></div>
            <div class="card"><div class="label">تاريخ الميلاد</div><div class="value">${safe(student.date_of_birth)}</div></div>
            <div class="card"><div class="label">مكان الولادة</div><div class="value">${safe(student.birth_place)}</div></div>
            <div class="card"><div class="label">الرقم الوطني</div><div class="value">${safe(student.national_id)}</div></div>
            <div class="card"><div class="label">المدينة / منطقة السكن</div><div class="value">${safe(student.city?.name)}</div></div>
          </div>

          <h2>الحالة الأكاديمية والتسجيل</h2>
          <div class="grid">
            <div class="card"><div class="label">تاريخ التسجيل بالمعهد</div><div class="value">${safe(student.enrollment_date)}</div></div>
            <div class="card"><div class="label">تاريخ بدء الحضور الفعلي</div><div class="value">${safe(student.start_attendance_date)}</div></div>
            <div class="card"><div class="label">حالة القيد</div><div class="value">${safe(student.status?.name)}</div></div>
            <div class="card"><div class="label">الفرع</div><div class="value">${safe(student.institute_branch?.name)}</div></div>
            <div class="card"><div class="label">الشعبة</div><div class="value">${safe(student.branch?.name)}</div></div>
            <div class="card"><div class="label">الدفعة الدراسية</div><div class="value">${safe(student.batch?.name)}</div></div>
            <div class="card"><div class="label">المدرسة الصباحية</div><div class="value">${safe(student.school?.name)}</div></div>
          </div>

          <h2>معلومات العائلة والأولياء</h2>
          <div class="grid" style="margin-bottom: 15px;">
            <div class="card"><div class="label">اسم الأب</div><div class="value">${
              father
                ? safe(
                    father.name || `${father.first_name} ${father.last_name}`,
                  )
                : "—"
            }</div></div>
            <div class="card"><div class="label">اسم الأم</div><div class="value">${
              mother
                ? safe(
                    mother.name || `${mother.first_name} ${mother.last_name}`,
                  )
                : "—"
            }</div></div>
          </div>
          <div class="grid">
            ${guardiansHtml}
          </div>

          <h2>العقد المالي والرسوم</h2>
          <div class="grid-3">
            <div class="card"><div class="label">المبلغ الإجمالي ($)</div><div class="value">${safe(
              student.enrollment_contract?.total_amount_usd,
            )}</div></div>
            <div class="card"><div class="label">قيمة الحسم الممنوح</div><div class="value">${safe(
              student.enrollment_contract?.discount_amount,
            )} ${student.enrollment_contract?.currency === "SYP" ? "SYP" : "$"}</div></div>
            <div class="card"><div class="label">نسبة الحسم (%)</div><div class="value">${safe(
              student.enrollment_contract?.discount_percentage,
            )}%</div></div>
            <div class="card"><div class="label">تاريخ بدء الأقساط</div><div class="value">${safe(
              student.enrollment_contract?.installments_start_date
                ? student.enrollment_contract.installments_start_date.split(
                    "T",
                  )[0]
                : "-",
            )}</div></div>
            <div class="card" style="grid-column: span 3; border-top: 2px solid #6F013F;"><div class="label">صافي المبلغ المستحق ($)</div><div class="value" style="font-size: 18px; color: #6F013F;">${safe(
              student.enrollment_contract?.final_amount_usd,
            )} USD</div></div>
          </div>

          <div class="footer">
            هذه الوثيقة مستخرجة آلياً من نظام معهد العلماء لإدارة الطلاب - مخصص للاستخدام الداخلي فقط
          </div>
        </body>
      </html>
    `;

    const win = window.open("", "", "width=1000,height=800");
    if (!win) return;
    win.document.write(html);
    win.document.close();
    win.print();
  };

  /* ================= EXCEL ================= */
  const handleExcel = () => {
    const guardiansText =
      guardians.length > 0
        ? guardians
            .map(
              (g) =>
                `${safe(g.relationship)}: ${safe(
                  g.name || `${g.first_name || ""} ${g.last_name || ""}`.trim(),
                )}`,
            )
            .join(" | ")
        : "—";

    const data = [
      {
        "الاسم الكامل": safe(student.full_name),
        الجنس: g(student.gender),
        "تاريخ الميلاد": safe(student.date_of_birth),
        "مكان الولادة": safe(student.birth_place),
        "الرقم الوطني": safe(student.national_id),
        المدينة: safe(student.city?.name),
        "تاريخ التسجيل": safe(student.enrollment_date),
        "بدء الحضور": safe(student.start_attendance_date),
        الحالة: safe(student.status?.name),
        "فرع المعهد": safe(student.institute_branch?.name),
        الشعبة: safe(student.branch?.name),
        الدفعة: safe(student.batch?.name),
        المدرسة: safe(student.school?.name),
        "اسم الأب": father
          ? safe(father.name || `${father.first_name} ${father.last_name}`)
          : "—",
        "اسم الأم": mother
          ? safe(mother.name || `${mother.first_name} ${mother.last_name}`)
          : "—",
        الأولياء: guardiansText,
        "كيف عرف المعهد": safe(student.how_know_institute),
        "الحالة الصحية": safe(student.health_status),
        "الحالة النفسية": safe(student.psychological_status),
        الباص: safe(student.bus?.name),
        السائق: safe(student.bus?.driver_name),
        "رقم اللوحة": safe(student.bus?.plate_number),
        "الإجمالي $": safe(student.enrollment_contract?.total_amount_usd),
        "قيمة الحسم": safe(student.enrollment_contract?.discount_amount),
        "الخصم %": safe(student.enrollment_contract?.discount_percentage),
        "الصافي $": safe(student.enrollment_contract?.final_amount_usd),
        "بدء الأقساط": safe(student.enrollment_contract?.installments_start_date?.split("T")[0]),
        ملاحظات: safe(student.notes),
      },
    ];

    const ws = XLSX.utils.json_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Student Report");

    const buf = XLSX.write(wb, { bookType: "xlsx", type: "array" });
    saveAs(
      new Blob([buf], { type: "application/octet-stream" }),
      "معلومات_الطالب.xlsx",
    );
  };

  return (
    <BaseModal
      open={open}
      onClose={onClose}
      widthClass="max-w-6xl"
      title={
        <div className="flex items-center justify-between w-full">
          <span>معلومات الطالب</span>
          <div className="flex gap-2 mr-2">
            <PrintButton onClick={handlePrint} />
            <ExcelButton onClick={handleExcel} />
          </div>
        </div>
      }
    >
      <div className="flex flex-col h-full">
        <div className="flex border-b border-gray-100 mb-6 gap-8">
          <TabButton
            active={activeTab === "info"}
            onClick={() => setActiveTab("info")}
            label="المعلومات العامة"
            icon={<User size={18} />}
          />
          <TabButton
            active={activeTab === "contacts"}
            onClick={() => setActiveTab("contacts")}
            label="جهات الاتصال"
            icon={<PhoneCall size={18} />}
          />
        </div>

        <div className="max-h-[65vh] overflow-y-auto pe-2 space-y-8 text-sm custom-scrollbar">
          {activeTab === "info" && (
            <>
              <Section title="المعلومات الأساسية" icon={<User size={16} />}>
                <Grid>
                  <Card label="الاسم الكامل" value={student.full_name} />
                  <Card label="الجنس" value={g(student.gender)} />
                  <Card label="تاريخ الميلاد" value={student.date_of_birth} />
                  <Card label="مكان الولادة" value={student.birth_place} />
                  <Card label="الرقم الوطني" value={student.national_id} />
                  <Card label="المدينة" value={student.city?.name} />
                </Grid>
              </Section>

              <Section
                title="التسجيل والدراسة"
                icon={<GraduationCap size={16} />}
              >
                <Grid>
                  <Card
                    label="فرع المعهد"
                    value={student.institute_branch?.name}
                  />
                  <Card label="الشعبة" value={student.branch?.name} />
                  <Card label="الدفعة" value={student.batch?.name} />
                  <Card label="المدرسة" value={student.school?.name} />
                  <Card label="الحالة" value={student.status?.name} />
                  <Card label="تاريخ التسجيل" value={student.enrollment_date} />
                  <Card
                    label="بدء الحضور"
                    value={student.start_attendance_date}
                  />
                </Grid>
              </Section>

              <Section title="العائلة" icon={<Home size={16} />}>
                <Grid>
                  <Card
                    label="الأب"
                    value={
                      father
                        ? father.name ||
                          `${father.first_name} ${father.last_name}`
                        : "—"
                    }
                  />
                  <Card label="الرقم الوطني للأب" value={father?.national_id} />
                  <Card
                    label="الأم"
                    value={
                      mother
                        ? mother.name ||
                          `${mother.first_name} ${mother.last_name}`
                        : "—"
                    }
                  />
                  <Card label="الرقم الوطني للأم" value={mother?.national_id} />
                </Grid>
              </Section>

              {guardians.length > 0 && (
                <Section title="الأولياء" icon={<User size={16} />}>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {guardians.map((guardian) => (
                      <div
                        key={guardian.id}
                        className="bg-white rounded-2xl p-4 shadow-sm border border-gray-50"
                      >
                        <div className="text-[#6F013F] font-semibold mb-2">
                          {guardian.relationship === "father"
                            ? "الأب"
                            : guardian.relationship === "mother"
                              ? "الأم"
                              : safe(guardian.relationship)}
                        </div>

                        <div className="space-y-1 text-sm">
                          <InfoRow
                            label="الاسم"
                            value={
                              guardian.name ||
                              `${guardian.first_name || ""} ${guardian.last_name || ""}`.trim()
                            }
                          />
                          <InfoRow
                            label="الرقم الوطني"
                            value={guardian.national_id}
                          />
                          <InfoRow
                            label="رقم قديم"
                            value={guardian.legacy_phone}
                          />
                          <InfoRow
                            label="جهة اتصال أساسية"
                            value={guardian.is_primary_contact ? "نعم" : "لا"}
                          />
                        </div>
                      </div>
                    ))}
                  </div>
                </Section>
              )}

              <Section title="النقل" icon={<Bus size={16} />}>
                <Grid>
                  <Card label="الباص" value={student.bus?.name} />
                  <Card label="اسم السائق" value={student.bus?.driver_name} />
                  <Card label="رقم اللوحة" value={student.bus?.plate_number} />
                </Grid>
              </Section>

              <Section title="الحالة الصحية" icon={<HeartPulse size={16} />}>
                <Grid>
                  <Card label="الحالة الصحية" value={student.health_status} />
                </Grid>
              </Section>

              <Section title="الحالة النفسية" icon={<Brain size={16} />}>
                <Grid>
                  <Card
                    label="الحالة النفسية"
                    value={student.psychological_status}
                  />
                </Grid>
              </Section>

              <Section title="بيانات إضافية" icon={<MapPin size={16} />}>
                <Grid>
                  <Card
                    label="كيف تعرف على المعهد"
                    value={student.how_know_institute}
                  />
                </Grid>
              </Section>

              <Section title="العقد المالي" icon={<FileText size={16} />}>
                <Grid cols={3}>
                  <Card
                    label="المبلغ الإجمالي ($)"
                    value={student.enrollment_contract?.total_amount_usd}
                    premium
                  />
                  <Card
                    label="قيمة الحسم"
                    value={student.enrollment_contract?.discount_amount}
                    variant="warning"
                  />
                  <Card
                    label="نسبة الحسم (%)"
                    value={
                      student.enrollment_contract?.discount_percentage + "%"
                    }
                  />
                  <Card
                    label="المبلغ الصافي ($)"
                    value={student.enrollment_contract?.final_amount_usd}
                    variant="success"
                  />
                  <Card
                    label="بدء الأقساط"
                    value={student.enrollment_contract?.installments_start_date?.split("T")[0]}
                  />
                </Grid>
              </Section>

              <Section title="ملاحظات" icon={<FileText size={16} />}>
                <div className="bg-white rounded-2xl p-4 text-gray-700 shadow-sm border border-gray-50">
                  {safe(student.notes)}
                </div>
              </Section>
            </>
          )}

          {activeTab === "contacts" && (
            <ContactsSummary
              apiData={contactsSummary}
              fallbackData={fallbackContactsData}
              isLoading={loadingContacts}
            />
          )}
        </div>
      </div>
    </BaseModal>
  );
}

/* ================= UI PARTS ================= */

function TabButton({ active, onClick, label, icon }) {
  return (
    <button
      onClick={onClick}
      className={`flex items-center gap-2 pb-3 px-1 border-b-2 transition-all duration-200 outline-none ${
        active
          ? "border-[#6F013F] text-[#6F013F] font-semibold"
          : "border-transparent text-gray-400 hover:text-gray-600"
      }`}
    >
      {icon}
      <span className="text-sm">{label}</span>
    </button>
  );
}

function ContactsSummary({ apiData, fallbackData, isLoading }) {
  if (isLoading) {
    return (
      <div className="py-20 flex flex-col items-center justify-center gap-3">
        <Spinner />
        <p className="text-gray-400">جاري تحميل جهات الاتصال...</p>
      </div>
    );
  }

  const rawData = apiData?.data || apiData || fallbackData || {};

  const personal = Array.isArray(rawData.personal_contacts)
    ? rawData.personal_contacts.map(normalizeContact)
    : [];

  const family = Array.isArray(rawData.family_contacts)
    ? rawData.family_contacts.map(normalizeContact)
    : [];

  const guardiansArr = Array.isArray(rawData.guardians_contacts)
    ? rawData.guardians_contacts.map((g) => ({
        ...g,
        details: Array.isArray(g.details)
          ? g.details.map(normalizeContact)
          : Array.isArray(g.contact_details)
            ? g.contact_details.map(normalizeContact)
            : [],
      }))
    : [];

  const hasNoContacts =
    personal.length === 0 &&
    family.length === 0 &&
    guardiansArr.every(
      (g) => (g.details || []).length === 0 && !g.legacy_phone,
    );

  if (hasNoContacts) {
    return (
      <div className="py-20 text-center text-gray-400">
        لا يوجد جهات اتصال مسجلة لهذا الطالب أو عائلته.
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {personal.length > 0 && (
        <ContactGroup
          title="الطالب (شخصي)"
          icon={<User size={16} />}
          items={personal}
        />
      )}

      {guardiansArr.map((g, idx) => {
        const details = g.details || [];
        if (details.length === 0 && !g.legacy_phone) return null;

        const relationshipTranslation = {
          father: "الأب",
          mother: "الأم",
        };

        return (
          <ContactGroup
            key={idx}
            title={`${
              relationshipTranslation[g.relationship] || safe(g.relationship)
            } — ${safe(g.name)}`}
            icon={<User size={16} />}
            items={details}
            legacyPhone={g.legacy_phone}
          />
        );
      })}

      {family.length > 0 && (
        <ContactGroup
          title="العائلة / المنزل"
          icon={<Home size={16} />}
          items={family}
        />
      )}
    </div>
  );
}

function ContactGroup({ title, icon, items = [], legacyPhone }) {
  return (
    <div className="space-y-3">
      <div className="flex items-center gap-2 text-[#6F013F] font-semibold">
        <div className="p-1.5 bg-[#fbeaf3] rounded-lg">{icon}</div>
        <span>{title}</span>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
        {items.map((contact, cIdx) => (
          <ContactCard key={cIdx} contact={contact} />
        ))}

        {items.length === 0 && legacyPhone && (
          <div className="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-4 flex items-center gap-3">
            <Phone size={16} className="text-gray-400" />
            <div>
              <span className="text-[10px] text-gray-400 block">
                رقم مسجل سابقاً
              </span>
              <span className="font-bold text-gray-600" dir="ltr">
                {legacyPhone}
              </span>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

function ContactCard({ contact }) {
  const contactTypeLabel = contact.is_landline
    ? "هاتف أرضي"
    : contact.type === "phone"
      ? "هاتف محمول"
      : safe(contact.type);

  return (
    <div className="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
      <div className="flex justify-between items-start mb-2 gap-3">
        <div>
          <span className="text-xs font-medium text-gray-400 block mb-1">
            {contactTypeLabel}
            {contact.owner_name ? ` — ${contact.owner_name}` : ""}
          </span>

          <div
            className="flex items-center gap-2 text-lg font-bold text-gray-800"
            dir="ltr"
          >
            <Phone size={16} className="text-gray-400" />
            <span>
              {safe(contact.full_phone_number || contact.phone_number)}
            </span>
          </div>
        </div>

        {contact.is_primary && (
          <span className="bg-green-50 text-green-600 text-[10px] px-2 py-0.5 rounded-full border border-green-100 font-medium whitespace-nowrap">
            أساسي
          </span>
        )}
      </div>

      <div className="flex flex-wrap gap-2 mt-3">
        {contact.supports_call && (
          <Badge icon={<PhoneCall size={10} />} label="اتصال" color="blue" />
        )}
        {contact.supports_whatsapp && (
          <Badge
            icon={<MessageCircle size={10} />}
            label="واتساب"
            color="green"
          />
        )}
        {contact.supports_sms && (
          <Badge
            icon={<MessageSquare size={10} />}
            label="رسائل"
            color="amber"
          />
        )}
      </div>

      {contact.notes && (
        <p className="mt-3 text-xs text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100">
          {contact.notes}
        </p>
      )}
    </div>
  );
}

function Badge({ icon, label, color }) {
  const colors = {
    blue: "bg-blue-50 text-blue-600 border-blue-100",
    green: "bg-green-50 text-green-600 border-green-100",
    amber: "bg-amber-50 text-amber-600 border-amber-100",
  };

  return (
    <div
      className={`flex items-center gap-1.5 px-2 py-0.5 rounded-lg border text-[10px] font-medium ${
        colors[color]
      }`}
    >
      {icon}
      <span>{label}</span>
    </div>
  );
}

function Section({ title, children, icon }) {
  return (
    <div className="group">
      <div className="flex items-center gap-3 mb-4">
        <div className="p-2 rounded-xl bg-[#FDF2F7] text-[#6F013F] group-hover:bg-[#6F013F] group-hover:text-white transition-all duration-300">
          {icon && <span>{icon}</span>}
        </div>
        <h3 className="font-bold text-lg text-gray-800 tracking-tight">
          {title}
        </h3>
        <div className="flex-1 h-[1.5px] bg-gradient-to-l from-transparent via-gray-100 to-gray-50 mr-2" />
      </div>
      <div className="ps-2">{children}</div>
    </div>
  );
}

function Grid({ children, cols = 2 }) {
  const colsClass =
    cols === 3
      ? "md:grid-cols-3"
      : cols === 4
        ? "md:grid-cols-4"
        : "md:grid-cols-2";

  return (
    <div className={`grid grid-cols-1 ${colsClass} gap-4`}>{children}</div>
  );
}

function Card({ label, value, variant = "default", premium = false }) {
  const styles = {
    default: "bg-white border-gray-100 text-gray-800",
    warning: "bg-amber-50/40 border-amber-100 text-amber-900",
    success: "bg-green-50/40 border-green-100 text-green-900",
    danger: "bg-red-50/40 border-red-100 text-red-900",
  };

  return (
    <div
      className={`group rounded-2xl p-4 shadow-sm border transition-all duration-300 hover:shadow-md hover:-translate-y-1 ${
        styles[variant]
      } ${premium ? "border-r-4 border-r-[#6F013F]" : ""}`}
    >
      <div className="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1 group-hover:text-[#6F013F] transition-colors">
        {label}
      </div>
      <div
        className={`font-semibold break-words ${
          premium ? "text-lg text-[#6F013F]" : "text-sm text-gray-700"
        }`}
      >
        {safe(value)}
      </div>
    </div>
  );
}

function InfoRow({ label, value }) {
  return (
    <div className="flex items-start justify-between gap-3 border-b border-gray-50 py-1.5 last:border-b-0">
      <span className="text-gray-500">{label}</span>
      <span className="font-medium text-gray-800 text-left">{safe(value)}</span>
    </div>
  );
}

function Spinner() {
  return (
    <div className="w-10 h-10 rounded-full border-4 border-gray-200 border-t-[#6F013F] animate-spin" />
  );
}
