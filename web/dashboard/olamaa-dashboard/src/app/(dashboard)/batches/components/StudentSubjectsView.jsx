"use client";
import { useMemo } from "react";

import DataTable from "@/components/common/DataTable";
import PrintExportActions from "@/components/common/PrintExportActions";
import StudentInfoCard from "./StudentInfoCard";
import Breadcrumb from "@/components/common/Breadcrumb";

export default function StudentSubjectsView({ student, batch, onBack }) {
  const currentBatch = batch || student?.batch;
  
  const subjects = useMemo(() => {
    const rawSubjects = currentBatch?.batch_subjects || currentBatch?.batchSubjects || [];
    return rawSubjects.map((bs) => ({
      id: bs.id,
      subject_name: bs.subject?.name || bs.name || "—",
      course_name: currentBatch?.name || "—",
    }));
  }, [currentBatch]);

  const columns = [
    { header: "المادة", key: "subject_name" },
    { header: "الدورة", key: "course_name" },
  ];

  return (
    <div
      dir="rtl"
      className="w-full min-h-screen p-4 md:p-6 bg-[#fcfcfd] space-y-6"
    >
      <div className="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
        <div className="flex flex-col gap-1">
          <h1 className="text-xl font-bold text-gray-800">مواد الطالب</h1>
          <Breadcrumb />
        </div>

        <div className="flex flex-wrap items-center gap-2">
          <PrintExportActions
            data={subjects}
            selectedIds={[]}
            columns={[
              { header: "#", key: "id" },
              { header: "المادة", key: "subject_name" },
              { header: "الدورة", key: "course_name" },
            ]}
            title="مواد الطالب"
            filename="مواد-الطالب"
          />

          {/* <button
            type="button"
            onClick={onBack}
            className="h-[38px] px-4 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition"
          >
            رجوع
          </button> */}
        </div>
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-12 gap-5">
        <div className="xl:col-span-9 rounded-2xl overflow-hidden">
          <DataTable
            data={subjects}
            columns={columns}
            pageSize={10}
            showCheckbox={false}
          />
        </div>
        <div className="xl:col-span-3">
          <StudentInfoCard
            student={{
              ...student,
              batch_name: currentBatch?.name || "—",
              registration_date: student?.registration_date || "—",
              subjects_count: subjects.length,
            }}
          />
        </div>
      </div>
    </div>
  );
}
