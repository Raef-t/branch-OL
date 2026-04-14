"use client";

import { useMemo, useState } from "react";
import dayjs from "dayjs";
import Breadcrumb from "@/components/common/Breadcrumb";
import DataTable from "@/components/common/DataTable";
import { useGetLogsQuery } from "@/store/services/logsApi";

import FiltersBar from "./components/FiltersBar";
import TimelineItem from "./components/TimelineItem";
import LogsSkeleton from "./components/LogsSkeleton";
import LogDetailsSheet from "./components/LogDetailsSheet";

const normalize = (v) => {
  if (v === undefined || v === null) return "";
  if (typeof v === "string" || typeof v === "number" || typeof v === "boolean")
    return String(v);
  try {
    return JSON.stringify(v);
  } catch {
    return String(v);
  }
};

const toObject = (v) =>
  v && typeof v === "object" && !Array.isArray(v) ? v : {};

const extractModelName = (auditableType = "") => {
  const parts = auditableType.split("\\");
  return parts[parts.length - 1] || auditableType || "—";
};

const computeDiffs = (log) => {
  const oldObj = toObject(log.old_values);
  const newObj = toObject(log.new_values);
  const keys = Array.from(
    new Set([...Object.keys(oldObj), ...Object.keys(newObj)]),
  );

  if (log.event === "created") {
    return keys
      .filter((k) => normalize(newObj[k]) !== "")
      .map((k) => ({ key: k, old: null, next: newObj[k] }));
  }

  if (log.event === "deleted") {
    return keys
      .filter((k) => normalize(oldObj[k]) !== "")
      .map((k) => ({ key: k, old: oldObj[k], next: null }));
  }

  // updated
  return keys
    .map((k) => {
      const oldV = normalize(oldObj[k]);
      const newV = normalize(newObj[k]);
      const changed = oldV !== newV;
      return changed ? { key: k, old: oldObj[k], next: newObj[k] } : null;
    })
    .filter(Boolean);
};

export default function LogsPage() {
  const { data, isLoading, isError, refetch } = useGetLogsQuery();

  const logs = data?.data || [];

  const [eventFilter, setEventFilter] = useState(""); // "", "created", "updated", "deleted"
  const [userFilter, setUserFilter] = useState(""); // user_name
  const [page, setPage] = useState(1);
  const [selectedLog, setSelectedLog] = useState(null);

  const perPage = 10;

  const enrichedLogs = useMemo(() => {
    return logs.map((log) => {
      const diffs = computeDiffs(log);
      const topKeys = diffs.slice(0, 3).map((d) => d.key);

      return {
        ...log,
        _diffs: diffs,
        _changedCount: diffs.length,
        _topKeys: topKeys,
        _modelName: extractModelName(log.auditable_type),
      };
    });
  }, [logs]);

  const usersOptions = useMemo(() => {
    const names = Array.from(
      new Set(enrichedLogs.map((l) => l.user_name).filter(Boolean)),
    );
    return [
      { value: "", label: "كل المستخدمين", key: "all-users" },
      ...names.map((n, idx) => ({ value: n, label: n, key: `u-${idx}-${n}` })),
    ];
  }, [enrichedLogs]);

  const eventOptions = useMemo(
    () => [
      { value: "", label: "كل الأنواع", key: "all-events" },
      { value: "created", label: "إضافة", key: "created" },
      { value: "updated", label: "تعديل", key: "updated" },
      { value: "deleted", label: "حذف", key: "deleted" },
    ],
    [],
  );

  const filteredLogs = useMemo(() => {
    let arr = [...enrichedLogs];

    if (eventFilter) arr = arr.filter((l) => l.event === eventFilter);
    if (userFilter)
      arr = arr.filter((l) => String(l.user_name) === String(userFilter));

    // الأحدث أولاً
    arr.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    return arr;
  }, [enrichedLogs, eventFilter, userFilter]);

  const columns = useMemo(
    () => [
      {
        header: "الوقت",
        key: "created_at",
        render: (val) => (
          <div className="text-gray-500 whitespace-nowrap text-xs">
            {dayjs(val).format("HH:mm  YYYY-MM-DD")}
          </div>
        ),
      },
      {
        header: "المستخدم",
        key: "user_name",
        render: (val) => (
          <span className="font-semibold text-gray-800">{val || "—"}</span>
        ),
      },
      {
        header: "نوع العملية",
        key: "event",
        render: (val) => {
          const labels = { created: "إضافة", updated: "تعديل", deleted: "حذف" };
          const colors = {
            created: "bg-blue-100 text-blue-700 border-blue-200",
            updated: "bg-green-100 text-green-700 border-green-200",
            deleted: "bg-red-100 text-red-700 border-red-200",
          };
          return (
            <span
              className={`px-2 py-0.5 rounded-full text-[11px] border ${
                colors[val] || colors.updated
              }`}
            >
              {labels[val] || val}
            </span>
          );
        },
      },
      {
        header: "العنصر",
        key: "auditable_type",
        render: (val, row) => {
          const model = row._modelName;
          return (
            <div className="text-[12px]">
              {model}{" "}
              <span className="text-gray-400">(ID: {row.auditable_id})</span>
            </div>
          );
        },
      },
      {
        header: "التغييرات",
        key: "_changedCount",
        render: (val, row) => (
          <div className="space-y-1">
            <span className="inline-flex items-center gap-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100 px-2 py-0.5 text-[11px]">
              {val || 0} حقل
            </span>
            <div className="flex flex-wrap gap-1">
              {row._topKeys?.length ? (
                row._topKeys.map((k) => (
                  <span
                    key={k}
                    className="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px]"
                  >
                    {k}
                  </span>
                ))
              ) : (
                <span className="text-[11px] text-gray-400">
                  لا تغييرات واضحة
                </span>
              )}
              {val > (row._topKeys?.length || 0) && (
                <span className="text-[11px] text-gray-400">
                  +{val - row._topKeys.length}
                </span>
              )}
            </div>
          </div>
        ),
      },
    ],
    [],
  );

  if (isLoading) return <LogsSkeleton count={7} />;
  if (isError)
    return (
      <div className="p-2 space-y-1">
        <Breadcrumb />
        <h1 className="text-2xl font-bold">سجل العمليات</h1>
        <div className="p-4 rounded-xl bg-white">
          فشل تحميل السجلات
          <button
            className="mr-2 text-pink-700 underline"
            onClick={() => refetch?.()}
          >
            إعادة المحاولة
          </button>
        </div>
      </div>
    );

  return (
    <div className="p-4 bg-gray-50/30 min-h-screen">
      <div className="mb-6">
        <Breadcrumb />
      </div>

      <div className="flex items-center justify-between gap-6 flex-wrap mb-8">
        <div className="space-y-1">
          <div className="flex items-center gap-3">
             <div className="w-2 h-8 bg-[#6F013F] rounded-full" />
             <h1 className="text-3xl font-black text-gray-900 tracking-tight">سجلات الأوديت</h1>
          </div>
          <p className="text-sm text-gray-500 font-medium mr-5">
            متابعة دقيقة لكافة العمليات والتحسينات المجراة على النظام
            <span className="mx-2 text-gray-300">|</span>
            إجمالي السجلات: <span className="text-[#6F013F] font-bold">{logs.length}</span>
          </p>
        </div>
        
        <div className="bg-white p-3 rounded-[2rem] shadow-sm border border-gray-100 min-w-[500px]">
          <FiltersBar
            eventValue={eventFilter}
            onEventChange={(v) => {
              setEventFilter(v);
              setPage(1);
            }}
            eventOptions={eventOptions}
            userValue={userFilter}
            onUserChange={(v) => {
              setUserFilter(v);
              setPage(1);
            }}
            userOptions={usersOptions}
            onReset={() => {
              setEventFilter("");
              setUserFilter("");
              setPage(1);
            }}
          />
        </div>
      </div>

      <div className="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden transition-all">
        <DataTable
          data={filteredLogs}
          columns={columns}
          isLoading={isLoading}
          showCheckbox={false}
          pageSize={perPage}
          currentPage={page}
          onPageChange={setPage}
          emptyMessage="لم يتم العثور على سجلات تطابق بحثك"
          getRowId={(row) => row.id}
          onRowClick={(row) => setSelectedLog(row)}
          rowClassName="hover:bg-[#6F013F]/[0.02] cursor-pointer transition-colors group"
          renderActions={(row) => (
            <button
              className="text-xs px-4 py-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-[#6F013F] hover:text-white hover:border-[#6F013F] transition-all duration-300 font-bold flex items-center gap-2 group-hover:shadow-md"
              onClick={(e) => {
                e.stopPropagation();
                setSelectedLog(row);
              }}
            >
              عرض التفاصيل
            </button>
          )}
          mobileRender={(row) => <TimelineItem log={row} />}
        />
      </div>

      <LogDetailsSheet log={selectedLog} onClose={() => setSelectedLog(null)} />
    </div>
  );
}
