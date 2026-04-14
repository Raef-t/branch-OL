"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import { ChevronDown, X } from "lucide-react";

export default function SearchableSelect({
  label,
  required,
  value,
  onChange,
  onAddNew,
  onQueryChange,
  options = [],
  placeholder = "اختر...",
  allowClear = true,
  disabled = false,
}) {
  const wrapRef = useRef(null);
  const inputRef = useRef(null);

  const selected = useMemo(
    () => options.find((o) => String(o.value) === String(value)) || null,
    [options, value],
  );

  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState("");
  const [activeIndex, setActiveIndex] = useState(-1);
  const listRef = useRef(null);

  // عند إغلاق القائمة، إذا كان هناك عنصر مختار نظهر اسمه
  useEffect(() => {
    if (!open) {
      setQuery(selected?.label || "");
      setActiveIndex(-1);
    }
  }, [selected?.label, open]);

  // إغلاق عند الضغط خارج العنصر
  useEffect(() => {
    const onDoc = (e) => {
      if (!wrapRef.current) return;
      if (!wrapRef.current.contains(e.target)) {
        setOpen(false);
      }
    };
    document.addEventListener("mousedown", onDoc);
    return () => document.removeEventListener("mousedown", onDoc);
  }, []);

  const filtered = useMemo(() => {
    const q = (query || "").trim().toLowerCase();
    // لما القائمة مفتوحة وما في بحث، أظهر كل الخيارات
    const list = !q
      ? options
      : options.filter((o) => (o.label || "").toLowerCase().includes(q));
    return list;
  }, [options, query]);

  // Reset active index when list changes
  useEffect(() => {
    setActiveIndex(-1);
  }, [filtered]);

  const handlePick = (opt) => {
    onChange?.(String(opt.value));
    setQuery(opt.label);
    onQueryChange?.(opt.label);
    setOpen(false);
  };

  const handleClear = (e) => {
    e.stopPropagation();
    onChange?.("");
    setQuery("");
    onQueryChange?.("");
    setOpen(false);
    inputRef.current?.focus();
  };

  const handleOpen = () => {
    if (disabled) return;
    setOpen(true);
    setQuery(""); // مهم جدًا: عند الفتح أظهر كل الخيارات
    onQueryChange?.("");
    setTimeout(() => inputRef.current?.focus(), 0);
  };

  const handleKeyDown = (e) => {
    if (!open) {
      if (e.key === "ArrowDown" || e.key === "ArrowUp" || e.key === "Enter") {
        handleOpen();
      }
      return;
    }

    if (e.key === "ArrowDown") {
      e.preventDefault();
      setActiveIndex((prev) => (prev < filtered.length - 1 ? prev + 1 : prev));
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      setActiveIndex((prev) => (prev > 0 ? prev - 1 : 0));
    } else if (e.key === "Enter") {
      e.preventDefault();
      if (activeIndex >= 0 && activeIndex < filtered.length) {
        handlePick(filtered[activeIndex]);
      } else if (filtered.length === 1) {
        handlePick(filtered[0]);
      } else if (onAddNew && query.trim() && filtered.length === 0) {
        onAddNew(query.trim());
        setOpen(false);
      }
    } else if (e.key === "Escape") {
      setOpen(false);
    }
  };

  // Scroll active item into view
  useEffect(() => {
    if (activeIndex >= 0 && listRef.current) {
      const activeEl = listRef.current.children[activeIndex];
      if (activeEl) {
        activeEl.scrollIntoView({ block: "nearest" });
      }
    }
  }, [activeIndex]);

  return (
    <div className="flex flex-col gap-1" ref={wrapRef}>
      {label && (
        <label className="text-sm text-gray-700 font-medium">
          {label}
          {required && <span className="text-pink-600">*</span>}
        </label>
      )}

      <div
        className={`relative w-full border border-gray-200 rounded-xl bg-white px-3 py-2.5 text-sm text-gray-700
        outline-none transition focus-within:border-[#D40078] focus-within:ring-1 focus-within:ring-[#F3C3D9]
        ${disabled ? "opacity-60 pointer-events-none" : ""}`}
        onClick={handleOpen}
      >
        <input
          ref={inputRef}
          value={open ? query : selected?.label || query}
          onChange={(e) => {
            setQuery(e.target.value);
            onQueryChange?.(e.target.value);
            setOpen(true);
          }}
          onFocus={() => {
            if (!open) handleOpen();
          }}
          onKeyDown={handleKeyDown}
          placeholder={placeholder}
          className="w-full bg-transparent outline-none pr-10"
        />

        {allowClear && !!value && (
          <button
            type="button"
            onClick={handleClear}
            className="absolute left-9 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700"
            title="مسح"
          >
            <X size={16} />
          </button>
        )}

        <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <ChevronDown size={18} />
        </span>

        {open && (
          <div className="absolute right-0 left-0 top-[calc(100%+8px)] z-[100] min-w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto custom-scrollbar">
            {filtered.length === 0 ? (
              <div className="p-1">
                <div className="px-3 py-2 text-gray-400 text-xs">
                  لا يوجد نتائج
                </div>
                {onAddNew && query.trim() && (
                  <button
                    type="button"
                    onClick={(e) => {
                      e.stopPropagation();
                      onAddNew(query.trim());
                      setOpen(false);
                    }}
                    className="w-full text-right px-3 py-2.5 bg-pink-50 text-[#6F013F] font-medium rounded-lg hover:bg-pink-100 transition flex items-center justify-between group"
                  >
                    <span>إضافة "{query.trim()}" كجديد</span>
                    <span className="text-[10px] bg-white px-2 py-0.5 rounded border border-pink-200 group-hover:border-pink-300">
                      جديد
                    </span>
                  </button>
                )}
              </div>
            ) : (
              <div className="p-1" ref={listRef}>
                {filtered.map((opt, idx) => (
                  <button
                    key={opt.key ?? `${opt.value}-${idx}`}
                    type="button"
                    onClick={(e) => {
                      e.stopPropagation();
                      handlePick(opt);
                    }}
                    onMouseEnter={() => setActiveIndex(idx)}
                    className={`w-full text-right px-3 py-2 rounded-lg transition mb-0.5
                    ${
                      String(opt.value) === String(value)
                        ? "bg-[#6F013F] text-white"
                        : idx === activeIndex
                          ? "bg-pink-100 text-[#6F013F]"
                          : "hover:bg-pink-50 text-gray-700"
                    }`}
                  >
                    {opt.label}
                  </button>
                ))}

                {onAddNew &&
                  query.trim() &&
                  !filtered.some((f) => f.label === query.trim()) && (
                    <div className="border-t border-gray-100 mt-1 pt-1">
                      <button
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          onAddNew(query.trim());
                          setOpen(false);
                        }}
                        className="w-full text-right px-3 py-2.5 text-[#6F013F] font-medium hover:bg-pink-50 rounded-lg transition flex items-center justify-between"
                      >
                        <span>إضافة "{query.trim()}"...</span>
                        <span className="text-[10px] text-gray-400 italic">
                          جديد
                        </span>
                      </button>
                    </div>
                  )}
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
