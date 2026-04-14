"use client";

import { useEffect, useMemo, useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { ChevronDown } from "lucide-react";
import { useDispatch } from "react-redux";
import { useRouter } from "next/navigation";
import {
  setAddExamModal,
  setAddPaymentModal,
  setAddStudentModal,
  setAddTeacherModal,
  setAddEmployeeModal,
} from "@/store/slices/uiSlice";

import { useGetPaymentEditRequestsQuery } from "@/store/services/paymentEditRequestsApi";
import { useGetExamResultEditRequestsQuery } from "@/store/services/examResultEditRequestsApi";

export default function Menu() {
  const [openMenu, setOpenMenu] = useState(null);
  const [userRoles, setUserRoles] = useState([]);
  const router = useRouter();
  const dispatch = useDispatch();

  /* ================= Load User ================= */

  useEffect(() => {
    const auth = localStorage.getItem("auth");
    if (auth) {
      try {
        const parsed = JSON.parse(auth);
        setUserRoles(parsed?.user?.roles || []);
      } catch {
        setUserRoles([]);
      }
    }
  }, []);

  const isAdmin = userRoles.includes("admin");

  /* ================= Pending Requests Count ================= */

  const { data: payRes } = useGetPaymentEditRequestsQuery(
    { status: "pending" },
    {
      skip: !isAdmin,
      pollingInterval: 10000,
      refetchOnFocus: true,
    },
  );

  const { data: gradeRes } = useGetExamResultEditRequestsQuery(
    { status: "pending" },
    {
      skip: !isAdmin,
      pollingInterval: 10000,
      refetchOnFocus: true,
    },
  );

  const toArray = (res) =>
    Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : [];

  const pendingCount = useMemo(() => {
    const pay = toArray(payRes).filter(
      (x) => String(x?.status).toLowerCase() === "pending",
    ).length;

    const grades = toArray(gradeRes).filter(
      (x) => String(x?.status).toLowerCase() === "pending",
    ).length;

    return pay + grades;
  }, [payRes, gradeRes]);

  /* ================= Helpers ================= */

  const toggleMenu = (title) => {
    setOpenMenu(openMenu === title ? null : title);
  };

  const hasAccess = (allowedRoles = []) => {
    if (!allowedRoles || allowedRoles.length === 0) return true;
    return allowedRoles.some((role) => userRoles.includes(role));
  };

  const handleLogout = async () => {
    try {
      const auth = localStorage.getItem("auth");
      const token = auth ? JSON.parse(auth)?.token : null;

      await fetch(`${process.env.NEXT_PUBLIC_API_BASE_URL}/logout`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    } catch {}

    localStorage.removeItem("auth");
    localStorage.removeItem("currentUser");
    router.replace("/login");
  };

  const handleAction = (href, e) => {
    if (href === "/exams/add") {
      e.preventDefault();
      dispatch(setAddExamModal(true));
      return true;
    }
    if (href === "/payments/add") {
      e.preventDefault();
      dispatch(setAddPaymentModal(true));
      return true;
    }
    if (href === "/students?add=1") {
      e.preventDefault();
      dispatch(setAddStudentModal(true));
      return true;
    }
    if (href === "/teachers?addTeacher=1") {
      e.preventDefault();
      dispatch(setAddTeacherModal(true));
      return true;
    }
    if (href === "/employees?addEmployee=1") {
      e.preventDefault();
      dispatch(setAddEmployeeModal(true));
      return true;
    }
    return false;
  };

  /* ================= Menu Items ================= */

  const menuItems = [
    {
      title: "الصفحة الرئيسية",
      icon: "/icons/ChartLine.svg",
      href: "/",
      roles: ["admin", "employee"],
    },
    {
      title: "الجداول الرئيسية",
      icon: "/icons/CirclesFour.svg",
      roles: ["admin"],
      sub: [
        { name: "سجلات الحضور والغياب", href: "/attendance", roles: ["admin"] },
        { name: "المدن", href: "/cities", roles: ["admin"] },
        { name: "الباصات", href: "/buses", roles: ["admin"] },
        {
          name: "السجلات الاكاديمية",
          href: "/academicBranches",
          roles: ["admin"],
        },
        { name: "المواد", href: "/subjects", roles: ["admin"] },
        { name: "طرق المعرفة بنا", href: "/knowWays", roles: ["admin"] },
        { name: "القاعات الدراسية", href: "/classRooms", roles: ["admin"] },
        { name: "نماذج الرسائل", href: "/message-templates", roles: ["admin"] },
        { name: "المدارس", href: "/schools", roles: ["admin"] },
      ],
    },
    {
      title: "المدرسون",
      icon: "/icons/UsersThree.svg",
      roles: ["admin"],
      sub: [
        { name: "قائمة المدرسين", href: "/teachers", roles: ["admin"] },
        {
          name: "إضافة مدرس",
          href: "/teachers?addTeacher=1",
          roles: ["admin"],
        },
      ],
    },
    {
      title: "الموظفون",
      icon: "/icons/HeadCircuit.svg",
      roles: ["admin"],
      sub: [
        { name: "قائمة الموظفين", href: "/employees", roles: ["admin"] },
        {
          name: "إضافة موظف",
          href: "/employees?addEmployee=1",
          roles: ["admin"],
        },
      ],
    },
    {
      title: "الطلاب و اولياء الامور",
      icon: "/icons/Student.svg",
      roles: ["admin", "employee"],
      sub: [
        {
          name: "قائمة الطلاب",
          href: "/students",
          roles: ["admin", "employee"],
        },
        { name: "إضافة طالب", href: "/students?add=1", roles: ["admin"] },
        { name: "قائمة العائلات", href: "/families", roles: ["admin"] },
        { name: "أولياء الأمور", href: "/guardians", roles: ["admin"] },
      ],
    },
    // {
    //   title: "العائلات وأولياء الأمور",
    //   icon: "/icons/UsersThree.svg",
    //   roles: ["admin"],
    //   sub: [
    //     { name: "قائمة العائلات", href: "/families", roles: ["admin"] },
    //     { name: "أولياء الأمور", href: "/guardians", roles: ["admin"] },
    //   ],
    // },
    {
      title: "المذاكرات",
      icon: "/icons/EyeSlash.svg",
      roles: ["admin", "accountant"],
      sub: [
        {
          name: "قائمة المذاكرات",
          href: "/exams",
          roles: ["admin", "accountant"],
        },
        {
          name: "إضافة مذاكرة",
          href: "/exams/add",
          roles: ["admin", "accountant"],
        },
      ],
    },
    // {
    //   title: "قائمة الدورات",
    //   icon: "/icons/Export.svg",
    //   roles: ["admin"],
    //   sub: [{ name: "الدورات", href: "/batches", roles: ["admin"] }],
    // },
    {
      title: "الدفعات",
      icon: "/icons/HandCoins.svg",
      roles: ["admin", "employee_accountant", "accountant"],
      sub: [
        {
          name: "عرض الدفعات",
          href: "/payments",
          roles: ["admin", "employee_accountant", "accountant"],
        },
        { name: "إضافة دفعة", href: "/payments/add", roles: ["admin"] },
        { name: "رسائل الاخطاء", href: "/debug-errors", roles: ["admin"] },
      ],
    },
    {
      title: "الدورات",
      icon: "/icons/Export.svg",
      roles: ["admin"],
      sub: [
        {
          name: "برنامج الدوام الرسمي",
          href: "/class-schedules",
          roles: ["admin"],
        },
        { name: "قائمة الدورات", href: "/batches", roles: ["admin"] },
        {
          name: "مسودات الجدولة الذكية",
          href: "/scheduler-drafts",
          roles: ["admin"],
        },
        {
          name: "معالج التوليد الذكي",
          href: "/scheduler-wizard",
          roles: ["admin"],
        },
      ],
    },
    {
      title: "التقارير",
      icon: "/icons/ChartBar.svg",
      roles: ["admin", "accountant"],
      sub: [
        {
          name: "بيانات الطلاب",
          href: "/reports/students-data",
          roles: ["admin"],
        },

        {
          name: "تقارير الحضور والغياب",
          href: "/reports/attendance",
          roles: ["admin", "accountant"],
        },
        {
          name: "التقارير الشهرية",
          href: "/reports/monthly",
          roles: ["admin", "accountant"],
        },
        {
          name: "تقرير المذاكرات",
          href: "/reports/exams",
          roles: ["admin", "accountant"],
        },
        {
          name: "تقارير الباصات",
          href: "/reports/branch-buses",
          roles: ["admin"],
        },
        {
          name: "أرقام هواتف الطلاب",
          href: "/reports/students-phones",
          roles: ["admin"],
        },
      ],
    },
    {
      title: "لوحة التحكم",
      icon: "/icons/component10.svg",
      roles: ["admin"],
      sub: [
        { name: "أفرع المعهد", href: "/instituteBranches", roles: ["admin"] },
        { name: "السجلات", href: "/logs", roles: ["admin"] },
        { name: "الإعدادات", href: "/settings", roles: ["admin"] },
        { name: "الطلبات", href: "/requests", roles: ["admin"] },
      ],
    },
  ];

  /* ================= UI ================= */

  return (
    <div className="w-full flex-1 text-right font-medium px-2 flex flex-col overflow-hidden">
      <div className="flex-1 overflow-y-auto mt-1 pr-1 text-[14px]">
        {menuItems
          .filter((menu) => hasAccess(menu.roles))
          .map((menu) => {
            const isOpen = openMenu === menu.title;

            return (
              <div key={menu.title} className="mb-1">
                {menu.sub ? (
                  <button
                    onClick={() => toggleMenu(menu.title)}
                    className="group flex items-center justify-between w-full rounded-lg
                               px-2.5 py-2 text-[#4D4D4D]
                               hover:bg-[#AD164C] hover:text-white transition"
                  >
                    <div className="flex items-center gap-2">
                      <Image
                        src={menu.icon}
                        alt=""
                        width={20}
                        height={20}
                        className="group-hover:brightness-0 group-hover:invert"
                      />
                      <span>{menu.title}</span>
                    </div>

                    <ChevronDown
                      className={`w-4 h-4 transition ${
                        isOpen ? "rotate-180" : ""
                      }`}
                    />
                  </button>
                ) : (
                  <Link
                    href={menu.href}
                    className="group flex items-center w-full rounded-lg
                               px-2.5 py-2 text-[#4D4D4D]
                               hover:bg-[#AD164C] hover:text-white transition"
                  >
                    <Image
                      src={menu.icon}
                      alt=""
                      width={20}
                      height={20}
                      className="group-hover:brightness-0 group-hover:invert"
                    />
                    <span className="mr-2">{menu.title}</span>
                  </Link>
                )}

                {menu.sub && (
                  <div
                    className={`overflow-hidden transition-all duration-300 ${
                      isOpen ? "max-h-screen mt-1" : "max-h-0"
                    }`}
                  >
                    <ul className="space-y-1 pr-6">
                      {menu.sub
                        .filter((item) => hasAccess(item.roles))
                        .map((item) => (
                          <li key={item.name}>
                            <Link
                              href={item.href}
                              onClick={(e) => handleAction(item.href, e)}
                              className="flex items-center justify-between px-2.5 py-1.5 rounded-md text-[13px]
                                         hover:bg-[#F7CBE3] hover:font-semibold transition"
                            >
                              <span>{item.name}</span>

                              {item.href === "/requests" &&
                                pendingCount > 0 && (
                                  <span
                                    className="min-w-[20px] h-[20px] px-1 rounded-full
                                                   bg-[#D40078] text-white text-[11px]
                                                   flex items-center justify-center"
                                  >
                                    {pendingCount}
                                  </span>
                                )}
                            </Link>
                          </li>
                        ))}
                    </ul>
                  </div>
                )}
              </div>
            );
          })}
      </div>

      <div className="pt-2 pb-4">
        <button
          onClick={handleLogout}
          className="group flex items-center gap-2 w-full text-[#7B0046]
                     hover:bg-[#AD164C] hover:text-white
                     rounded-lg px-2.5 py-2 transition"
        >
          <Image
            src="/icons/SignOut.svg"
            alt="logout"
            width={20}
            height={20}
            className="group-hover:brightness-0 group-hover:invert"
          />
          تسجيل الخروج
        </button>
      </div>
    </div>
  );
}
