"use client";

import React, { useEffect, useState } from "react";
import Image from "next/image";

export default function ExamCard({ exams = [], isLoading = false }) {
  const [currentIndex, setCurrentIndex] = useState(0);

  const total = exams.length;
  const completed = exams.filter((e) => e.status === "completed").length;
  const remaining = Math.max(total - completed, 0);
  const progress = total > 0 ? (completed / total) * 100 : 0;

  // عند تغير المذاكرات نعيد المؤشر للأول
  useEffect(() => {
    setCurrentIndex(0);
  }, [total]);

  // تشغيل الكاروسل التلقائي
  useEffect(() => {
    if (total <= 1) return;
    const interval = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % total);
    }, 4500); // تغيير كل 4.5 ثواني
    return () => clearInterval(interval);
  }, [total]);

  const currentExam = exams[currentIndex] || null;

  if (isLoading) {
    return (
      <div className="relative w-full h-[185px] rounded-[18px] bg-gray-200 animate-pulse flex items-center justify-center">
        <span className="text-gray-400">جاري تحميل البيانات...</span>
      </div>
    );
  }

  return (
    <div className="relative w-full h-[205px] md:h-[195px] py-4 overflow-visible flex flex-col justify-center">
      {/* الخلفية */}
      <div className="absolute inset-0 rounded-[18px] bg-[#B80F57] shadow-sm" />
      <div className="absolute inset-0 rounded-[18px] bg-gradient-to-l from-[#A20F5E] to-[#C0125B]" />

      {/* النجمة العلوية */}
      <span className="absolute left-[132px] md:left-[145px] top-[22px] text-white text-[34px] md:text-[38px] leading-none opacity-95 z-20">
        ✦
      </span>

      {/* صورة الطالبة - طالعة برا الكارد */}
      <div className="absolute left-0 -top-[18px] bottom-0 w-[140px] md:w-[150px] z-30 flex items-end">
        <Image
          src="/wonam.svg"
          alt="طالبة"
          width={175}
          height={210}
          priority
          className="object-contain w-[140px] md:w-[165px] h-[118%]"
        />
      </div>

      {/* المحتوى */}
      <div
        dir="rtl"
        className="relative z-20 w-full pr-5 md:pr-6 pl-[135px] md:pl-[165px] flex flex-col justify-center items-end text-right h-full"
      >
        <div className="w-full flex items-center justify-between mb-3">
          <h2 className="text-white font-bold text-[17px] md:text-[20px] leading-tight m-0">
            عدد المذاكرات اليوم {total}
          </h2>

          {/* نقاط الكاروسل */}
          {total > 1 && (
            <div className="flex gap-[4px] mr-3" dir="ltr">
              {exams.map((_, i) => (
                <button
                  key={i}
                  onClick={() => setCurrentIndex(i)}
                  className={`h-1.5 rounded-full transition-all duration-300 ${
                    i === currentIndex
                      ? "w-4 bg-white"
                      : "w-1.5 bg-white/40 hover:bg-white/70"
                  }`}
                  aria-label={`Show exam ${i + 1}`}
                />
              ))}
            </div>
          )}
        </div>

        <div className="text-white text-[12px] md:text-[14px] leading-relaxed max-w-[360px] opacity-95 min-h-[72px] md:min-h-[64px] flex items-center justify-end">
          {currentExam ? (
            <p className="m-0 animate-pulse-once" key={currentIndex}>
              سيؤدي الطلاب اختبار{" "}
              <strong className="text-[#FFD7E5] font-bold">
                {currentExam.name || "المادة"}
              </strong>
              <br />
              اليوم في تمام الساعة{" "}
              <strong className="text-[#FFD7E5] font-bold" dir="ltr">
                {currentExam.exam_time?.slice(0, 5) || "--:--"}
              </strong>
              {currentExam.room ? ` في ${currentExam.room}` : ""}
              {currentExam.status === "completed" && (
                <span className="mr-2 text-[#4ade80] font-bold text-[11px] bg-white/10 px-1.5 py-0.5 rounded">
                  (مكتمل)
                </span>
              )}
            </p>
          ) : (
            <p className="m-0 mt-2">لا يوجد مذاكرات مجدولة لهذا اليوم حالياً.</p>
          )}
        </div>

        {/* خط التقدم */}
        <div className="mt-4 w-full flex items-center gap-3" dir="ltr">
          <span className="text-white font-semibold text-[14px] leading-none min-w-[30px]">
            {completed}/{total}
          </span>

          <div className="relative h-[8px] flex-1 rounded-full bg-[#8E0A49]">
            <div
              className="absolute left-0 top-0 h-full rounded-full bg-white transition-all duration-500"
              style={{ width: `${progress}%` }}
            />

            {/* النجوم داخل الجزء المتبقي من الشريط */}
            {remaining > 0 &&
              Array.from({ length: remaining }).map((_, i) => {
                const left =
                  progress + ((i + 1) * (100 - progress)) / (remaining + 1);

                return (
                  <span
                    key={i}
                    className="absolute top-1/2 text-white leading-none opacity-95"
                    style={{
                      left: `${left}%`,
                      transform: "translate(-50%, -50%)",
                      fontSize: i === 0 ? "14px" : "12px",
                    }}
                  >
                    ✦
                  </span>
                );
              })}
          </div>
        </div>
      </div>
    </div>
  );
}
