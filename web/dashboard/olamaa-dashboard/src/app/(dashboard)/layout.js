"use client";
import { useState } from "react";
import { useDispatch } from "react-redux";

import Navbar from "../../components/layout/Navbar";
import Sidebar from "../../components/layout/Sidebar";
import AuthGate from "@/components/common/AuthGate";
import "../globals.css";
import { useEffect } from "react";
import api from "@/lib/config/axiosConfig";
import { AlertTriangle } from "lucide-react";
import GlobalActionModals from "@/components/layout/GlobalActionModals";
export default function DashbaordLayout({ children }) {
  //const { list } = useSelector((state) => state.branches);
  const [selectedBranchId, setSelectedBranchId] = useState("");
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [isSystemStopped, setIsSystemStopped] = useState(false);
  const [isAdmin, setIsAdmin] = useState(false);
  const dispatch = useDispatch();

  useEffect(() => {
    // 2) Check if user is admin
    const checkAdmin = () => {
      let _isAdmin = false;
      const raw =
        localStorage.getItem("auth") ||
        localStorage.getItem("authData") ||
        localStorage.getItem("user");
      if (raw) {
        try {
          const parsed = JSON.parse(raw);
          const u = parsed.user || parsed;
          const roles = Array.isArray(u?.roles)
            ? u.roles.map((r) => (typeof r === "string" ? r : r.name))
            : [];
          _isAdmin = roles.includes("admin");
          setIsAdmin(_isAdmin);
        } catch (e) {}
      }
      return _isAdmin;
    };

    // 1) Fetch system status
    const fetchSystemStatus = async (isAdminUser) => {
      if (!isAdminUser) return; // Only admins need this for the banner
      try {
        const res = await api.get("/settings");
        if (res.data?.data) {
          setIsSystemStopped(!res.data.data.is_system_enabled);
        }
      } catch (error) {
        // Silently ignore 403 or network timeouts to keep console clean
      }
    };

    const admin = checkAdmin();
    fetchSystemStatus(admin);
  }, []);

  return (
    <AuthGate>
      <div dir="rtl" className="h-dvh flex overflow-hidden">
        <Sidebar sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />

        <div className="flex-1 flex flex-col h-dvh min-w-0 overflow-hidden">
          {isSystemStopped && isAdmin && (
            <div className="bg-orange-500 text-white px-6 py-2 flex items-center justify-between text-sm animate-pulse z-50">
              <div className="flex items-center gap-2">
                <AlertTriangle size={18} />
                <span className="font-bold">
                  تنبيه: النظام متوقف حالياً عن العمل لجميع المستخدمين (وضع
                  الصيانة)
                </span>
              </div>
              <span className="hidden md:inline bg-white/20 px-2 py-0.5 rounded text-[11px]">
                أنت تشاهد لوحة التحكم بصفتك مديراً
              </span>
            </div>
          )}
          <Navbar
            //  branches={list}
            selectedBranchId={selectedBranchId}
            setSelectedBranchId={setSelectedBranchId}
          />
          <main className="flex-1 overflow-y-auto">{children}</main>
        </div>
        <GlobalActionModals />
      </div>
    </AuthGate>
  );
}
