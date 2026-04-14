"use client";

import { useEffect, useRef, useState, useCallback } from "react";
import { createPortal } from "react-dom";
import { Clock, X } from "lucide-react";

/** Helpers */
function pad2(n) {
  return String(n).padStart(2, "0");
}

function hour12To24(hour12, period) {
  let h = Number(hour12);
  if (period === "AM") return h === 12 ? 0 : h;
  return h === 12 ? 12 : h + 12;
}

function hour24To12(hour24) {
  const h = Number(hour24);
  if (h === 0) return 12;
  if (h > 12) return h - 12;
  return h;
}

function parseTime(value) {
  if (!value) return null;
  const raw = String(value).trim().toUpperCase();
  if (!raw) return null;

  let m = raw.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
  if (m) {
    const hh = Number(m[1]);
    const mm = Number(m[2]);
    const period = m[3].toUpperCase();
    if (hh < 1 || hh > 12 || mm < 0 || mm > 59) return null;
    return {
      hour12: pad2(hh),
      minute: pad2(mm),
      period,
      hour24: pad2(hour12To24(hh, period)),
      formatted: `${pad2(hh)}:${pad2(mm)} ${period}`,
    };
  }

  m = raw.match(/^(\d{1,2}):(\d{2})$/);
  if (m) {
    const hh24 = Number(m[1]);
    const mm = Number(m[2]);
    if (hh24 < 0 || hh24 > 23 || mm < 0 || mm > 59) return null;
    const period = hh24 >= 12 ? "PM" : "AM";
    const hh12 = hour24To12(hh24);
    return {
      hour12: pad2(hh12),
      minute: pad2(mm),
      period,
      hour24: pad2(hh24),
      formatted: `${pad2(hh12)}:${pad2(mm)} ${period}`,
    };
  }
  return null;
}

/** Circular Clock Component */
function CircularClock({ type, value, onChange }) {
  const isHours = type === "hours";
  const items = isHours 
    ? [12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] 
    : [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55];

  // Reduced size
  const size = 180;
  const centerX = size / 2;
  const centerY = size / 2;
  // Radius for the numbers placement. Reduced to accommodate 180px, ensuring numbers don't touch the edge.
  const radius = 64; 

  const currentVal = Number(value);
  const angle = isHours ? (currentVal % 12) * 30 : (currentVal % 60) * 6;

  const handleClick = (e) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const x = e.clientX - rect.left - centerX;
    const y = e.clientY - rect.top - centerY;
    
    let rad = Math.atan2(y, x);
    let deg = (rad * 180) / Math.PI + 90;
    if (deg < 0) deg += 360;

    let finalVal;
    if (isHours) {
      finalVal = Math.round(deg / 30);
      if (finalVal === 0) finalVal = 12;
    } else {
      // Snap to nearest 5 minutes
      finalVal = Math.round((deg / 6) / 5) * 5;
      if (finalVal === 60) finalVal = 0;
    }
    onChange(pad2(finalVal));
  };


  return (
    <div 
      className="relative bg-gray-100/60 rounded-full mx-auto select-none cursor-pointer"
      style={{ width: size, height: size }}
      onMouseDown={handleClick}
    >
      {items.map((item, idx) => {
        const itemAngle = (idx * 30) * (Math.PI / 180);
        const tx = Math.sin(itemAngle) * radius;
        const ty = -Math.cos(itemAngle) * radius;
        const isSelected = isHours ? currentVal === item : (currentVal === item || (currentVal % 5 !== 0 && Math.abs(currentVal - item) < 2.5));

        return (
          <div
            key={item}
            className={`absolute flex items-center justify-center w-7 h-7 text-[12px] font-semibold
              ${isSelected ? "text-white z-10" : "text-gray-500"}
            `}
            style={{
              left: centerX + tx - 14,
              top: centerY + ty - 14,
            }}
          >
            {isHours ? item : pad2(item)}
          </div>
        );
      })}

      {/* Clock Hand (No transitions) */}
      <div 
        className="absolute bottom-1/2 left-1/2 w-[2px] bg-[#AD164C] origin-bottom"
        style={{
          height: `${radius}px`,
          transform: `translateX(-50%) rotate(${angle}deg)`,
        }}
      >
        {/* End dot */}
        <div className="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-[#AD164C] shadow-md" />
      </div>
      {/* Center dot */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#AD164C]" />
    </div>
  );
}

export default function TimePickerSmart({
  label,
  value,
  onChange,
  placeholder = "hh:mm AM",
  required = false,
  disabled = false,
  allowClear = true,
}) {
  const wrapRef = useRef(null);
  const [mounted, setMounted] = useState(false);
  const [open, setOpen] = useState(false);
  const [view, setView] = useState("hours");
  // Reduced dropdown width
  const [pos, setPos] = useState({ top: 0, left: 0, width: 240 });

  const [hour, setHour] = useState("12");
  const [minute, setMinute] = useState("00");
  const [period, setPeriod] = useState("AM");
  const [localInput, setLocalInput] = useState("");

  useEffect(() => setMounted(true), []);

  useEffect(() => {
    const p = parseTime(value);
    if (p) {
      setHour(p.hour12);
      setMinute(p.minute);
      setPeriod(p.period);
      setLocalInput(p.formatted);
    } else {
      setLocalInput(value || "");
    }
  }, [value]);

  const updatePosition = useCallback(() => {
    const el = wrapRef.current;
    if (!el) return;

    const rect = el.getBoundingClientRect();
    // Smaller height reflecting the smaller clock and padding
    const dropdownHeight = 350; 
    const dropdownWidth = 240;
    const margin = 12;

    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;

    const shouldFlip = spaceBelow < dropdownHeight && spaceAbove > dropdownHeight;

    let top = shouldFlip ? rect.top - dropdownHeight - 6 : rect.bottom + 6;
    let left = rect.left;

    if (left + dropdownWidth > window.innerWidth - margin) {
      left = window.innerWidth - dropdownWidth - margin;
    }
    if (left < margin) left = margin;

    setPos({ top, left, width: dropdownWidth });
  }, []);

  useEffect(() => {
    if (!open) return;
    updatePosition();
    window.addEventListener("resize", updatePosition);
    window.addEventListener("scroll", updatePosition, true);
    return () => {
      window.removeEventListener("resize", updatePosition);
      window.removeEventListener("scroll", updatePosition, true);
    };
  }, [open, updatePosition]);

  const toggleOpen = () => {
    if (disabled) return;
    if (!open) {
      updatePosition();
      setOpen(true);
      setView("hours");
    } else {
      setOpen(false);
    }
  };

  const handleCommit = (h = hour, m = minute, p = period) => {
    const formatted = `${h}:${m} ${p}`;
    onChange?.(formatted);
  };

  const handleManualInput = (e) => {
    let v = e.target.value.toUpperCase();
    const prev = localInput || "";

    if (!open) {
      updatePosition();
      setOpen(true);
    }

    if (v.length > prev.length) {
      v = v.replace(/[^0-9: AMPM]/g, "");

      if (/^[3-9]$/.test(v)) {
        v = "0" + v + ":";
        setView("minutes");
      } else if (/^\d{2}$/.test(v)) {
        let h = parseInt(v, 10);
        if (h > 12 && h <= 23) {
          v = pad2(h - 12) + ":";
          setPeriod("PM");
        } else if (h === 0 || h === 24) {
          v = "12:";
          setPeriod("AM");
        } else if (h > 24) {
          v = v.charAt(0);
        } else {
          v = v + ":";
        }
        setView("minutes");
      } else if (/^\d{3}$/.test(v)) {
        v = v.slice(0, 2) + ":" + v.charAt(2);
        setView("minutes");
      }

      if (/^\d{1,2}:\d{2}$/.test(v)) {
        let parts = v.split(':');
        let m = parseInt(parts[1], 10);
        if (m > 59) {
            v = parts[0] + ":59";
        }
        v = v + " " + period;
      }
    }

    setLocalInput(v);

    const p = parseTime(v);
    if (p) {
      setHour(p.hour12);
      setMinute(p.minute);
      setPeriod(p.period);
      onChange?.(p.formatted);
    } else {
      let hMatch = v.match(/^(\d{1,2})$/);
      if (hMatch) {
         let h = Number(hMatch[1]);
         if (h >= 1 && h <= 12) setHour(pad2(h));
         setView("hours");
      } else {
         let mMatch = v.match(/^(\d{1,2}):(\d{1,2})/);
         if (mMatch) {
            let h = Number(mMatch[1]);
            let mn = Number(mMatch[2]);
            if (h >= 1 && h <= 12) setHour(pad2(h));
            if (mn >= 0 && mn <= 59) setMinute(pad2(mn));
            setView("minutes");
         }
      }
    }
  };

  const handleKeyDown = (e) => {
    if (e.key === "ArrowUp" || e.key === "ArrowDown") {
      e.preventDefault();
      const newPeriod = period === "AM" ? "PM" : "AM";
      setPeriod(newPeriod);
      
      const newFormatted = `${hour}:${minute} ${newPeriod}`;
      
      if (!localInput) {
         setLocalInput(newFormatted);
      } else if (localInput.includes("AM") || localInput.includes("PM")) {
         setLocalInput(localInput.replace(/(AM|PM)/, newPeriod));
      } else if (localInput.match(/^\d{1,2}:\d{2}$/)) {
         setLocalInput(localInput + " " + newPeriod);
      }
      onChange?.(newFormatted);
    }
    if (e.key === "Enter") {
      e.preventDefault();
      setOpen(false);
    }
  };

  return (
    <div className="flex flex-col gap-1.5 w-full items-start">
      {label && (
        <label className="text-[13px] font-semibold text-gray-700 mr-1 flex items-center gap-1">
          {label}
          {required && <span className="text-red-500">*</span>}
        </label>
      )}

      <div
        ref={wrapRef}
        className={`group relative flex items-center bg-white border border-gray-200 rounded-2xl px-3 py-2.5 
          ${open ? "border-[#AD164C] ring-2 ring-[#AD164C]/10 shadow-sm" : "hover:border-gray-300 hover:shadow-sm"}
          ${disabled ? "opacity-50 grayscale cursor-not-allowed" : "cursor-pointer"}
          w-full
        `}
        onClick={toggleOpen}
      >
        <Clock className={`w-4 h-4 ${open ? "text-[#AD164C]" : "text-gray-400"}`} />
        <input
          value={localInput}
          onChange={handleManualInput}
          onKeyDown={handleKeyDown}
          className="flex-1 bg-transparent px-2.5 text-sm font-medium outline-none text-left"
          placeholder={placeholder}
          dir="ltr"
          disabled={disabled}
          onClick={(e) => { e.stopPropagation(); if (!open && !disabled) { updatePosition(); setOpen(true); } }}
        />
        {allowClear && localInput && (
          <button
            type="button"
            onClick={(e) => { e.stopPropagation(); setLocalInput(""); onChange?.(""); }}
            className="p-1 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600"
          >
            <X size={14} />
          </button>
        )}
      </div>

      {mounted && open && createPortal(
        <>
          <div className="fixed inset-0 z-[10000]" onClick={() => setOpen(false)} />
          <div
            className={`fixed z-[10001] bg-white border border-gray-100 rounded-[24px] shadow-[0_20px_60px_-10px_rgba(0,0,0,0.2)] overflow-hidden`}
            style={{ top: pos.top, left: pos.left, width: pos.width }}
            dir="ltr"
          >
            <div className="bg-[#6F013F] p-4 text-white text-center space-y-1">
              <div className="text-[9px] opacity-70 font-bold uppercase tracking-widest">Select Time</div>
              <div className="flex items-center justify-center gap-1 text-3xl font-bold">
                <button onClick={() => setView("hours")} className={view === "hours" ? "opacity-100" : "opacity-40"}>{hour}</button>
                <span className="opacity-30">:</span>
                <button onClick={() => setView("minutes")} className={view === "minutes" ? "opacity-100" : "opacity-40"}>{minute}</button>
                <div className="flex flex-col gap-0.5 ml-2 font-bold">
                  {["AM", "PM"].map(p => (
                    <button
                      key={p}
                      onClick={() => { setPeriod(p); handleCommit(hour, minute, p); }}
                      className={`text-[10px] px-1.5 py-0.5 rounded ${period === p ? "bg-white text-[#6F013F]" : "opacity-40"}`}
                    >
                      {p}
                    </button>
                  ))}
                </div>
              </div>
            </div>

            <div className="p-4">
              <CircularClock 
                type={view} 
                value={view === "hours" ? hour : minute} 
                onChange={(v) => {
                  if (view === "hours") { setHour(v); setView("minutes"); handleCommit(v, minute, period); } 
                  else { setMinute(v); handleCommit(hour, v, period); }
                }}
              />
            </div>

            <div className="px-4 py-3 bg-gray-50 flex items-center justify-between border-t border-gray-100">
               <button type="button" onClick={() => setOpen(false)} className="text-[11px] font-bold text-gray-400 hover:text-gray-600">CANCEL</button>
               <div className="flex gap-1.5">
                  <button type="button" onClick={(e) => { e.stopPropagation(); setLocalInput(""); onChange?.(""); setOpen(false); }} className="px-3 py-1.5 text-[11px] font-bold text-[#AD164C] hover:bg-pink-50 rounded-lg">CLEAR</button>
                  <button type="button" onClick={() => setOpen(false)} className="px-4 py-1.5 rounded-lg bg-[#6F013F] text-white text-[11px] font-bold">SET</button>
               </div>
            </div>
          </div>
        </>,
        document.body
      )}
    </div>
  );
}

