"use client";

import { useState, useEffect } from "react";
import {
  ChevronLeft,
  ChevronRight,
  ChevronsLeft,
  ChevronsRight,
} from "lucide-react";

function buildPages(page, totalPages, siblingCount = 1) {
  const totalNumbers = siblingCount * 2 + 5;
  if (totalPages <= totalNumbers) {
    return Array.from({ length: totalPages }, (_, i) => i + 1);
  }

  const leftSibling = Math.max(page - siblingCount, 1);
  const rightSibling = Math.min(page + siblingCount, totalPages);

  const showLeftDots = leftSibling > 2;
  const showRightDots = rightSibling < totalPages - 1;

  const firstPage = 1;
  const lastPage = totalPages;

  if (!showLeftDots && showRightDots) {
    const leftRange = Array.from(
      { length: 3 + siblingCount * 2 },
      (_, i) => i + 1,
    );
    return [...leftRange, "...", lastPage];
  }

  if (showLeftDots && !showRightDots) {
    const rightStart = totalPages - (2 + siblingCount * 2);
    const rightRange = Array.from(
      { length: 3 + siblingCount * 2 },
      (_, i) => rightStart + i,
    );
    return [firstPage, "...", ...rightRange];
  }

  const middleRange = Array.from(
    { length: siblingCount * 2 + 1 },
    (_, i) => leftSibling + i,
  );

  return [firstPage, "...", ...middleRange, "...", lastPage];
}

export default function Pagination({
  page = 1,
  totalPages = 1,
  onPageChange,
  className = "",
  hideIfSinglePage = false,
  siblingCount = 1,
}) {
  // --- منطق تقليل الأرقام في الموبايل ---
  const [isMobile, setIsMobile] = useState(false);

  useEffect(() => {
    const checkIsMobile = () => setIsMobile(window.innerWidth < 640);
    checkIsMobile();
    window.addEventListener("resize", checkIsMobile);
    return () => window.removeEventListener("resize", checkIsMobile);
  }, []);

  // في الموبايل نستخدم 0 كعد للأرقام المجاورة لتصغير الحجم
  const effectiveSiblingCount = isMobile ? 0 : siblingCount;

  const isSingle = totalPages <= 1;
  if (hideIfSinglePage && isSingle) return null;

  const goTo = (p) => {
    if (!onPageChange) return;
    const next = Math.min(Math.max(1, Number(p) || 1), totalPages);
    onPageChange(next);
  };

  const canPrev = page > 1;
  const canNext = page < totalPages;

  const pages = buildPages(page, totalPages, effectiveSiblingCount);

  // ====== Responsive Visual Theme ======
  const shell =
    "inline-flex items-center gap-1 sm:gap-1.5 rounded-full px-1.5 sm:px-2.5 py-1 sm:py-1.5 " +
    "bg-white/70 backdrop-blur shadow-xs max-w-full overflow-hidden";

  const iconBtn =
    "h-7 w-7 sm:h-8 sm:w-8 inline-flex items-center justify-center rounded-full border border-gray-200 " +
    "bg-white text-gray-700 transition-all duration-300 " +
    "hover:bg-[#F4D3E3] hover:border-[#F4D3E3] hover:text-gray-900 hover:scale-[1.08] hover:shadow-md " +
    "active:scale-[0.95] " +
    "disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:bg-white disabled:hover:shadow-none";

  const numBtn =
    "h-7 min-w-7 px-1.5 sm:h-8 sm:min-w-8 sm:px-2.5 inline-flex items-center justify-center rounded-full border border-gray-200 " +
    "text-[12px] sm:text-[13px] font-medium bg-white text-gray-700 transition-all duration-300 " +
    "hover:bg-[#F4D3E3] hover:border-[#F4D3E3] hover:text-gray-900 hover:scale-[1.08] hover:shadow-md " +
    "active:scale-[0.95]";

  const numActive =
    "h-8 min-w-8 sm:h-9 sm:min-w-9 px-2 sm:px-3 text-[13px] sm:text-[14px] " +
    "bg-[#F4D3E3] border-[#F4D3E3] text-gray-900 " +
    "shadow-md scale-[1.1]";

  const dots = "px-0.5 sm:px-1 text-gray-400 select-none text-xs sm:text-base";

  return (
    <div className={`flex justify-center mt-2 w-full px-2 ${className}`}>
      <div className={shell}>
        {/* FIRST (hidden on mobile to save space) */}
        <button
          disabled={!canPrev}
          onClick={() => goTo(1)}
          className={`${iconBtn} hidden sm:inline-flex`}
          aria-label="الذهاب للصفحة الأولى"
          title="الذهاب للصفحة الأولى"
        >
          <ChevronsRight size={16} />
        </button>

        <button
          disabled={!canPrev}
          onClick={() => goTo(page - 1)}
          className={iconBtn}
          aria-label="الصفحة السابقة"
          title="الصفحة السابقة"
        >
          <ChevronRight size={16} />
        </button>

        {/* NUMBERS */}
        <div className="flex items-center gap-1 sm:gap-1.5 px-0.5" dir="rtl">
          {pages.map((p, idx) =>
            p === "..." ? (
              <span key={`dots-${idx}`} className={dots}>
                …
              </span>
            ) : (
              <button
                key={p}
                onClick={() => goTo(p)}
                className={`${numBtn} ${p === page ? numActive : ""}`}
                aria-current={p === page ? "page" : undefined}
                aria-label={`الذهاب للصفحة ${p}`}
                title={`الذهاب للصفحة ${p}`}
              >
                {p}
              </button>
            ),
          )}
        </div>

        <button
          disabled={!canNext}
          onClick={() => goTo(page + 1)}
          className={iconBtn}
          aria-label="الصفحة التالية"
          title="الصفحة التالية"
        >
          <ChevronLeft size={16} />
        </button>

        {/* LAST (hidden on mobile to save space) */}
        <button
          disabled={!canNext}
          onClick={() => goTo(totalPages)}
          className={`${iconBtn} hidden sm:inline-flex`}
          aria-label="الذهاب للصفحة الأخيرة"
          title="الذهاب للصفحة الأخيرة"
        >
          <ChevronsLeft size={16} />
        </button>
      </div>
    </div>
  );
}

