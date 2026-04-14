"use client";

import { useEffect, useState } from "react";
import Pagination from "@/components/common/Pagination";
import TableSkeleton from "@/components/common/TableSkeleton";
import { ChevronUp, ChevronDown, ChevronsUpDown } from "lucide-react";

/**
 * Reusable DataTable Component
 *
 * @param {Array} data - The raw data array
 * @param {Array} columns - Column definitions [{ header: string, key: string, render: (val, row) => JSX }]
 * @param {Boolean} isLoading - Loading state
 * @param {Array} selectedIds - Selected row IDs
 * @param {Function} onSelectChange - Selection callback
 * @param {Number} pageSize - Rows per page
 * @param {Function} renderActions - Action column renderer (row, isMobile) => JSX
 * @param {String} emptyMessage - Message shown when data is empty
 * @param {String} rowIdKey - Key for row identity (default 'id')
 * @param {Boolean} showCheckbox - Whether to show checkboxes
 * @param {Function} mobileRender - Custom mobile card renderer
 */
export default function DataTable({
  data = [],
  columns = [],
  isLoading = false,
  selectedIds = [],
  onSelectChange,
  pageSize = 6,
  renderActions,
  emptyMessage = "لا توجد بيانات",
  rowIdKey = "id",
  getRowId, // New prop
  showCheckbox = true,
  mobileRender,
  onRowClick,
  // Server-side pagination props
  serverSide = false,
  currentPage = 1,
  totalPages: serverTotalPages,
  onPageChange: onServerPageChange,
  className = "",
}) {
  const safeData = Array.isArray(data) ? data : [];

  const getInternalId = (row) => {
    if (getRowId) return String(getRowId(row));
    return String(row[rowIdKey]);
  };

  // ===== Sorting =====
  const [sortConfig, setSortConfig] = useState({ key: null, direction: null });

  const handleSort = (key, sortKey) => {
    const finalKey = sortKey || key;
    let direction = "asc";
    if (sortConfig.key === finalKey && sortConfig.direction === "asc") {
      direction = "desc";
    } else if (sortConfig.key === finalKey && sortConfig.direction === "desc") {
      direction = null;
    }
    setSortConfig({ key: direction ? finalKey : null, direction });
  };

  const sortedData = [...safeData].sort((a, b) => {
    if (!sortConfig.key) return 0;

    const getVal = (obj, key) => {
      if (!key) return "";
      return key.split(".").reduce((o, i) => (o ? o[i] : ""), obj);
    };

    const aVal = getVal(a, sortConfig.key);
    const bVal = getVal(b, sortConfig.key);

    if (aVal === bVal) return 0;
    if (aVal === null || aVal === undefined) return 1;
    if (bVal === null || bVal === undefined) return -1;

    const comparison = String(aVal).localeCompare(String(bVal), "ar", {
      numeric: true,
    });
    return sortConfig.direction === "asc" ? comparison : -comparison;
  });

  // ===== Pagination =====
  const [localPage, setLocalPage] = useState(1);

  const page = serverSide ? currentPage : localPage;

  const setPage = (p) => {
    if (serverSide) {
      onServerPageChange?.(p);
    } else {
      setLocalPage(p);
      onServerPageChange?.(p);
    }
  };

  const finalData = serverSide ? safeData : sortedData;
  const totalPages = serverSide
    ? serverTotalPages
    : Math.ceil(finalData.length / pageSize) || 1;
  const paginated = serverSide
    ? finalData
    : finalData.slice((page - 1) * pageSize, page * pageSize);

  useEffect(() => {
    if (!serverSide) setLocalPage(1);
  }, [safeData.length, serverSide]);

  useEffect(() => {
    if (page > totalPages && totalPages > 0) {
      setPage(1);
    }
  }, [page, totalPages]);

  // ===== Checkbox =====
  const toggleSelect = (row) => {
    if (!onSelectChange) return;

    const id = getInternalId(row);
    const exists = selectedIds.map(String).includes(String(id));
    const updated = exists
      ? selectedIds.filter((sid) => sid !== id)
      : [...selectedIds, id];

    onSelectChange(updated);
  };

  const totalColumns = (showCheckbox ? 1 : 0) + columns.length + (renderActions ? 1 : 0);

  return (
    <div className={`bg-white shadow-sm rounded-xl border border-gray-200 p-5 w-full ${className}`}>
      {/* ================= DESKTOP ================= */}
      <div className="hidden md:block overflow-x-auto">
        <table className="min-w-full text-sm text-right border-separate border-spacing-y-2">
          <thead>
            <tr className="bg-pink-50 text-gray-700 font-medium">
              <th className="p-3 text-center rounded-r-xl w-16">#</th>
              {columns.map((col, i) => {
                const actualKey = col.sortKey || col.key;
                const isSorted = sortConfig.key === actualKey;
                const direction = isSorted ? sortConfig.direction : null;

                return (
                  <th
                    key={i}
                    className={`p-3 select-none ${col.className || ""} ${col.sortable !== false ? "cursor-pointer hover:bg-pink-100 transition-colors" : ""}`}
                    onClick={() =>
                      col.sortable !== false && handleSort(col.key, col.sortKey)
                    }
                  >
                    <div className="flex items-center gap-2">
                      <span>{col.header}</span>
                      {col.sortable !== false && (
                        <div className="flex items-center text-gray-400">
                          {direction === "asc" ? (
                            <ChevronUp size={14} className="text-[#6F013F]" />
                          ) : direction === "desc" ? (
                            <ChevronDown size={14} className="text-[#6F013F]" />
                          ) : (
                            <ChevronsUpDown size={14} className="opacity-40" />
                          )}
                        </div>
                      )}
                    </div>
                  </th>
                );
              })}
              {renderActions && (
                <th className="p-3 text-center rounded-l-xl w-32">الإجراءات</th>
              )}
            </tr>
          </thead>

          <tbody>
            {isLoading ? (
              <tr>
                <td colSpan={totalColumns} className="p-0">
                  <TableSkeleton
                    headers={[]} // Not used for structure here
                    rows={pageSize}
                    showCheckbox={showCheckbox}
                    actionCount={2}
                  />
                </td>
              </tr>
            ) : !safeData.length ? (
              <tr>
                <td colSpan={totalColumns} className="p-20 text-center text-gray-400 font-medium bg-gray-50/30 rounded-xl">
                  {emptyMessage}
                </td>
              </tr>
            ) : (
              paginated.map((row, index) => {
                const id = getInternalId(row);
                const isLastColumnRounded = !renderActions;

                return (
                  <tr
                    key={id}
                    className={`bg-white hover:bg-pink-50 transition border-b border-gray-50 ${onRowClick ? "cursor-pointer" : ""}`}
                    onClick={() => onRowClick?.(row)}
                  >
                    <td className="p-3 text-center rounded-r-xl">
                      <div className="flex items-center justify-center gap-2">
                        {showCheckbox && (
                          <input
                            type="checkbox"
                            className="w-4 h-4 accent-[#6F013F]"
                            checked={selectedIds.map(String).includes(String(id))}
                            onClick={(e) => e.stopPropagation()}
                            onChange={() => toggleSelect(row)}
                          />
                        )}
                        <span>{(page - 1) * pageSize + index + 1}</span>
                      </div>
                    </td>

                    {columns.map((col, i) => {
                      const isLast = isLastColumnRounded && i === columns.length - 1;
                      return (
                        <td
                          key={i}
                          className={`p-3 ${col.className || ""} ${isLast ? "rounded-l-xl" : ""}`}
                        >
                          {col.render
                            ? col.render(row[col.key], row, index, page, pageSize)
                            : (row[col.key] ?? "—")}
                        </td>
                      );
                    })}

                    {renderActions && (
                      <td className="p-3 rounded-l-xl text-center">
                        {renderActions(row, false)}
                      </td>
                    )}
                  </tr>
                );
              })
            )}
          </tbody>
        </table>
      </div>

      {/* ================= MOBILE ================= */}
      <div className="md:hidden space-y-4 mt-4">
        {paginated.map((row, index) => {
          const id = getInternalId(row);
          if (mobileRender)
            return mobileRender(
              row,
              index,
              page,
              pageSize,
              toggleSelect,
              selectedIds.includes(id),
            );

          return (
            <div
              key={id}
              className={`border border-gray-200 rounded-xl p-4 shadow-sm ${onRowClick ? "cursor-pointer hover:bg-pink-50 transition" : ""}`}
              onClick={() => onRowClick?.(row)}
            >
              <div className="flex justify-between items-center mb-3">
                <div className="flex items-center gap-2">
                  <span className="text-gray-500">#</span>
                  <span className="font-semibold">
                    {(page - 1) * pageSize + index + 1}
                  </span>
                  {showCheckbox && (
                    <input
                      type="checkbox"
                      className="w-4 h-4 accent-[#6F013F]"
                      checked={selectedIds.map(String).includes(String(id))}
                      onClick={(e) => e.stopPropagation()}
                      onChange={() => toggleSelect(row)}
                    />
                  )}
                </div>
                <div onClick={(e) => e.stopPropagation()}>
                  {renderActions && renderActions(row, true)}
                </div>
              </div>

              {columns.map((col, i) => (
                <div key={i} className="flex justify-between mb-2">
                  <span className="text-gray-500">{col.header}:</span>
                  <span className="font-medium text-left">
                    {col.render
                      ? col.render(row[col.key], row, index, page, pageSize)
                      : (row[col.key] ?? "—")}
                  </span>
                </div>
              ))}
            </div>
          );
        })}
      </div>

      {/* ================= PAGINATION ================= */}
      <Pagination page={page} totalPages={totalPages} onPageChange={setPage} />
    </div>
  );
}
