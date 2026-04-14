"use client";

import { useEffect, useMemo, useState } from "react";
import { useSelector } from "react-redux";
import Breadcrumb from "@/components/common/Breadcrumb";
import EmployeesTable from "./components/EmployeesTable.js";
import CredentialsModal from "./components/CredentialsModal";
import api from "@/lib/config/axiosConfig";
import toast from "react-hot-toast";

const tabs = [
  { key: "all", label: "الكل" },
  { key: "admin", label: "الإدارة" },
  { key: "accountant", label: "المحاسبون" },
  { key: "supervisor", label: "المشرفون" },
];

export default function SettingsPage() {
  const [systemStopped, setSystemStopped] = useState(false);
  const [activeTab, setActiveTab] = useState("all");
  const [selectedIds, setSelectedIds] = useState([]);
  const [employees, setEmployees] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [credentialsEmployee, setCredentialsEmployee] = useState(null);
  const [isBackingUp, setIsBackingUp] = useState(false);
  const selectedBranchId = useSelector((state) => state.search.values.branch);

  // جلب الإعدادات عند التحميل
  const fetchSettings = async () => {
    try {
      const res = await api.get("/settings");
      if (res.data?.data) {
        setSystemStopped(!res.data.data.is_system_enabled);
      }
    } catch (error) {
      console.error("Error fetching settings", error);
    }
  };

  // جلب الموظفين عند التحميل
  const fetchEmployees = async () => {
    try {
      setIsLoading(true);
      const res = await api.get("/employees");
      if (res.data?.data) {
        const mapped = res.data.data.map((emp) => {
          let roles = emp.user?.roles || [];
          
          return {
            id: emp.id,
            userId: emp.user?.id,
            name: `${emp.first_name || ""} ${emp.last_name || ""}`.trim(),
            email: emp.email || "",
            username: emp.user?.unique_id || "",
            password: "Pass1234",
            avatar: emp.photo_url || `https://ui-avatars.com/api/?name=${emp.first_name}&background=random`,
            section: emp.job_type || "other",
            roles: roles,
            accountType: emp.user ? "toggle" : "create",
            isActive: emp.is_active,
            isApproved: emp.user?.is_approved ?? false,
            institute_branch_id: emp.institute_branch_id,
          };
        });
        setEmployees(mapped);
      }
    } catch (error) {
      console.error("Error fetching employees", error);
      toast.error("حدث خطأ أثناء جلب الموظفين");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchSettings();
    fetchEmployees();
  }, []);

  const filteredEmployees = useMemo(() => {
    let filtered = employees;

    // 1) Geographic Filtering (Branch)
    if (selectedBranchId) {
      filtered = filtered.filter(
        (emp) => String(emp.institute_branch_id) === String(selectedBranchId)
      );
    }

    // 2) Role/Tab Filtering
    if (activeTab === "all") return filtered;

    return filtered.filter((employee) => {
      // Check if section matches OR if roles array includes the active tab
      return (
        employee.section === activeTab ||
        (Array.isArray(employee.roles) && employee.roles.includes(activeTab))
      );
    });
  }, [employees, activeTab, selectedBranchId]);

  useEffect(() => {
    setSelectedIds([]);
  }, [activeTab]);

  const handleToggleSystem = async () => {
    const newState = !systemStopped;
    setSystemStopped(newState);
    try {
      await api.put("/settings", {
        is_system_enabled: !newState,
        maintenance_message: newState ? "النظام متوقف من قبل الإدارة" : "",
      });
      toast.success(newState ? "تم إيقاف النظام" : "تم تفعيل النظام");
    } catch (error) {
      console.error(error);
      setSystemStopped(!newState); // revert
      toast.error("فشل تغيير حالة النظام");
    }
  };

  const handleBackup = async () => {
    try {
      setIsBackingUp(true);
      const toastId = toast.loading("جاري إعداد النسخة الاحتياطية وتنزيلها...");
      
      const res = await api.get("/settings/backup", {
        responseType: "blob",
      });
      
      const url = window.URL.createObjectURL(new Blob([res.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", `backup-${new Date().toISOString().split('T')[0]}.sql`);
      document.body.appendChild(link);
      link.click();
      link.parentNode.removeChild(link);
      
      toast.dismiss(toastId);
      toast.success("تم تحميل النسخة الاحتياطية بنجاح");
    } catch (error) {
      console.error(error);
      toast.dismiss();
      toast.error("فشل في أخذ النسخة الاحتياطية");
    } finally {
      setIsBackingUp(false);
    }
  };

  // تنشيط أو تعطيل سجل الموظف (صفة الموظف في النظام)
  const handleToggleAccount = async (id, currentStatus) => {
    const newStatus = !currentStatus;
    setEmployees((prev) =>
      prev.map((emp) => (emp.id === id ? { ...emp, isActive: newStatus } : emp))
    );
    try {
      await api.put(`/employees/${id}`, { is_active: newStatus });
      toast.success("تم تحديث حالة الموظف");
    } catch (error) {
      setEmployees((prev) =>
        prev.map((emp) => (emp.id === id ? { ...emp, isActive: currentStatus } : emp))
      );
      toast.error("فشل تحديث حالة الموظف");
    }
  };

  // اعتماد أو إيقاف حساب المستخدم (User Account Approval)
  const handleToggleApproval = async (employeeId, userId, currentStatus) => {
    if (!userId) {
      toast.error("لا يوجد حساب مرتبط بهذا الموظف");
      return;
    }
    const newStatus = !currentStatus;
    setEmployees((prev) =>
      prev.map((emp) =>
        emp.id === employeeId ? { ...emp, isApproved: newStatus } : emp
      )
    );
    try {
      await api.post(`/users/${userId}/toggle-status`, { status: newStatus });
      toast.success(newStatus ? "تم اعتماد الحساب" : "تم إيقاف الحساب");
    } catch (error) {
      setEmployees((prev) =>
        prev.map((emp) =>
          emp.id === employeeId ? { ...emp, isApproved: currentStatus } : emp
        )
      );
      toast.error("فشل تحديث حالة اعتماد الحساب");
    }
  };

  // إنشاء حساب موظف
  const handleCreateAccount = async (employee) => {
    try {
      const toastId = toast.loading("جاري إنشاء الحساب...");
      const res = await api.post(`/employees/${employee.id}/activate-user`);
      toast.dismiss(toastId);
      
      const newUser = res.data?.data;
      if (newUser) {
        toast.success("تم إنشاء الحساب بنجاح");
        // Update local state to reflect that account is created
        setEmployees((prev) => prev.map(emp => {
          if (emp.id === employee.id) {
            return {
              ...emp,
              userId: newUser.id,
              username: newUser.unique_id,
              accountType: "toggle",
              roles: newUser.roles || []
            };
          }
          return emp;
        }));
        
        // Show credentials modal
        setCredentialsEmployee({
          ...employee,
          username: newUser.unique_id,
          password: "Pass1234",
        });
      }
    } catch (error) {
      toast.dismiss();
      toast.error(error.response?.data?.message || "حدث خطأ أثناء إنشاء الحساب");
    }
  };

  const handleAssignRole = async (employeeId, userId, role) => {
    if (!userId) {
      toast.error("يجب إنشاء حساب للموظف أولاً");
      return;
    }
    try {
      const toastId = toast.loading("جاري إضافة الدور...");
      await api.post(`/users/${userId}/roles`, { role });
      toast.dismiss(toastId);
      toast.success("تم التعيين بنجاح");
      
      setEmployees(prev => prev.map(emp => {
        if(emp.id === employeeId && !emp.roles.includes(role)) {
           return { ...emp, roles: [...emp.roles, role] };
        }
        return emp;
      }));
    } catch (error) {
      toast.dismiss();
      toast.error(error.response?.data?.message || "فشل تعيين الدور");
    }
  };

  const handleRemoveRole = async (employeeId, userId, role) => {
    if (!userId) return;
    try {
      const toastId = toast.loading("جاري إزالة الدور...");
      await api.delete(`/users/${userId}/roles/${role}`);
      toast.dismiss(toastId);
      toast.success("تم الإزالة بنجاح");
      
      setEmployees(prev => prev.map(emp => {
        if(emp.id === employeeId) {
           return { ...emp, roles: emp.roles.filter(r => r !== role) };
        }
        return emp;
      }));
    } catch (error) {
      toast.dismiss();
      toast.error(error.response?.data?.message || "فشل إزالة الدور");
    }
  };

  return (
    <div dir="rtl" className="w-full h-full p-6 flex flex-col gap-6">
      {/* HEADER */}
      <div className="w-full flex flex-col gap-5 items-start">
        <div className="flex flex-col text-right">
          <Breadcrumb />
        </div>

        <div className="flex items-center justify-between w-full">
          <div className="flex items-center gap-4">
            <span className="text-sm font-medium text-gray-500">
              ايقاف النظام بشكل كامل
            </span>

            <button
              type="button"
              onClick={handleToggleSystem}
              className={`relative w-14 h-7 rounded-full transition ${
                systemStopped ? "bg-[#6F013F]" : "bg-gray-300"
              }`}
            >
              <span
                className={`absolute top-1 w-5 h-5 rounded-full bg-white shadow transition-all ${
                  systemStopped ? "right-1" : "left-1"
                }`}
              />
            </button>
          </div>

          <button
            type="button"
            onClick={handleBackup}
            disabled={isBackingUp}
            className="flex items-center gap-2 bg-[#6F013F] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-50 hover:bg-[#5a0033]"
          >
            {isBackingUp ? (
              <span className="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full" />
            ) : (
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
            )}
            أخذ نسخة احتياطية للبيانات
          </button>
        </div>
      </div>

      {/* TABS + TABLE */}
      <div className="w-full">
        <ul className="flex flex-wrap w-full text-sm font-medium text-center mr-10 ">
          {tabs.map((tab) => {
            const isActive = activeTab === tab.key;

            return (
              <li key={tab.key} className="me-2">
                <button
                  type="button"
                  aria-current={isActive ? "page" : undefined}
                  onClick={() => setActiveTab(tab.key)}
                  className={`inline-block px-12 py-4 rounded-t-xl transition ${
                    isActive
                      ? " bg-white text-black border-x border-t border-gray-200"
                      : "text-[#6F013F] bg-pink-50 mx-2"
                  }`}
                >
                  {tab.label}
                </button>
              </li>
            );
          })}
        </ul>

        <div className="-mt-[1px]">
          <EmployeesTable
            employees={filteredEmployees}
            isLoading={isLoading}
            selectedIds={selectedIds}
            onSelectChange={setSelectedIds}
            onToggleAccount={handleToggleAccount}
            onToggleApproval={handleToggleApproval}
            onCreateAccount={handleCreateAccount}
            onViewCredentials={setCredentialsEmployee}
            onAssignRole={handleAssignRole}
            onRemoveRole={handleRemoveRole}
          />
        </div>
      </div>

      <CredentialsModal
        employee={credentialsEmployee}
        onClose={() => setCredentialsEmployee(null)}
      />
    </div>
  );
}
