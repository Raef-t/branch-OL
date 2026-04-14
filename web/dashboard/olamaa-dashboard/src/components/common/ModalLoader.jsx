"use client";

import Spinner from "./Spinner";

/**
 * ModalLoader - Simple white-box spinner with backdrop
 * Used specifically for modals that are fetching data before they can render
 */
export default function ModalLoader({ message = "جاري التحميل..." }) {
  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-[2px]">
      <div className="rounded-2xl bg-white p-10 shadow-2xl flex flex-col items-center gap-4 border border-gray-100 animate-in fade-in zoom-in duration-200">
        <Spinner className="w-12 h-12" />
        {message && (
          <p className="text-sm font-medium text-gray-500 tracking-wide">
            {message}
          </p>
        )}
      </div>
    </div>
  );
}
