import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/service_locator/get_it_service_locator.dart';
import '/features/attendance/data/repositories/attendance_repositories_implementation.dart';
import '/features/attendance/presentation/managers/cubits/attendance_cubit.dart';
import '/features/attendance/presentation/view/attendance_view.dart';
import '/features/auth/data/repositories/auth_repositories_implementation.dart';
import '/features/auth/presentation/managers/cubits/auth_cubit.dart';
import '/features/auth/presentation/view/auth_view.dart';
import '/features/class/data/repositories/batch_students/batch_students_repositories_implementation.dart';
import '/features/class/data/repositories/send_m_t_n_message/send_m_t_n_message_repositories_implementation.dart';
import '/features/class/presentation/managers/cubits/batch_students/batch_students_cubit.dart';
import '/features/class/presentation/managers/cubits/send_m_t_n_message/send_m_t_n_message_cubit.dart';
import '/features/class/presentation/view/class_view.dart';
import '/features/courses/data/repositories/academic_branches/academic_branches_repositories_implementation.dart';
import '/features/courses/data/repositories/total_students/total_students_repositories_implementation.dart';
import '/features/courses/presentation/managers/cubit/academic_branches/academic_branches_cubit.dart';
import '/features/courses/presentation/managers/cubit/total_students/total_students_cubit.dart';
import '/features/courses/presentation/view/courses_view.dart';
import '/features/courses_details/data/repositories/academic_branches_repositories_courses_details_implementation.dart';
import '/features/courses_details/presentation/managers/cubit/academic_branches_courses_details_cubit.dart';
import '/features/courses_details/presentation/view/courses_details_view.dart';
import '/features/details_marks_to_batch/data/repositories/exams_result_to_batch_repositories_implementation.dart';
import '/features/details_marks_to_batch/presentation/managers/cubits/exams_result_to_batch_cubit.dart';
import '/features/details_students/data/repositories/details_students/details_students_repositories_implementation.dart';
import '/features/details_students/data/repositories/financial_summary/financial_summary_repositories_implementation.dart';
import '/features/details_students/data/repositories/mark_students/mark_students_repositories_implementation.dart';
import '/features/details_students/data/repositories/monthly_evaluations/monthly_evaluations_repositories_implementation.dart';
import '/features/details_students/presentation/managers/cubits/details_students/details_students_cubit.dart';
import '/features/details_students/presentation/managers/cubits/financial_summary/financial_summary_cubit.dart';
import '/features/details_students/presentation/managers/cubits/mark_students/mark_students_cubit.dart';
import '/features/details_students/presentation/managers/cubits/monthly_evaluations/monthly_evaluations_cubit.dart';
import '/features/details_students/presentation/view/details_students_view.dart';
import '/features/exams_to_all_students/data/repositories/exams_to_all_students_repositories_implementation.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_cubit.dart';
import '/features/exams_to_all_students/presentation/view/exams_to_all_students_view.dart';
import '/features/exams_to_student/data/repositories/student_exams_repositories_implementation.dart';
import '/features/exams_to_student/presentation/managers/cubits/student_exams_cubit.dart';
import '/features/exams_to_student/presentation/view/exams_to_student_view.dart';
import '/features/filter_attendance/presentation/view/filter_attendance_view.dart';
import '/features/filter_exams/data/repositories/subjects/subjects_repositories_implementation.dart';
import '/features/filter_exams/presentation/managers/cubits/subjects/subjects_cubit.dart';
import '/features/filter_exams/presentation/view/filter_exams/filter_exams_view.dart';
import '/features/home/data/repositories/batch_average/batch_average_repositories_implementation.dart';
import '/features/home/data/repositories/institute_branch/institute_branch_repositories_implementation.dart';
import '/features/home/presentation/managers/cubits/batch_average/batch_average_cubit.dart';
import '/features/home/presentation/managers/cubits/institute_branch/institute_branch_cubit.dart';
import '/features/home/presentation/view/home_view.dart';
import '/features/details_marks_to_batch/presentation/view/details_marks_to_batch_view.dart';
import '/features/mark_to_batch/data/repositories/last_and_current_week_exams_to_batch_repositories_implementation.dart';
import '/features/mark_to_batch/presentation/managers/cubits/last_and_current_weeks_exams_to_batch_cubit.dart';
import '/features/mark_to_batch/presentation/view/mark_to_batch_view.dart';
import '/features/marks_to_student/presentation/view/marks_to_student.dart';
import '/features/notifications/presentation/view/notifications_view.dart';
import '/features/payments_to_student/presentation/view/payments_to_student_view.dart';
import '/features/profile/data/repositories/edit_first_and_last_name_employee/employee_repositories_implementation.dart';
import '/features/profile/data/repositories/edit_photo_employee/photo_employee_repositories_implementation.dart';
import '/features/profile/data/repositories/logout/log_out_repositories_implementation.dart';
import '/features/profile/presentation/managers/cubits/edit_first_and_last_name_employee/employee_cubit.dart';
import '/features/profile/presentation/managers/cubits/edit_photo_employee/photo_employee_cubit.dart';
import '/features/profile/presentation/managers/cubits/logout/log_out_cubit.dart';
import '/features/profile/presentation/view/profile_view.dart';
import '/features/q_r/data/repositories/door_session_repositories_implementation.dart';
import '/features/q_r/presentation/managers/cubits/door_session_cubit.dart';
import '/features/q_r/presentation/view/q_r_view.dart';
import '/features/scan/data/repositories/scan_qr_repositories_implementation.dart';
import '/features/scan/presentation/managers/cubits/scan_qr_cubit.dart';
import '/features/scan/presentation/view/scan_view.dart';
import '/features/search/presentation/view/search_view.dart';
import '/features/splash/presentation/view/splash_view.dart';
import '/features/teachers/data/repositories/teachers_repositories_implementation.dart';
import '/features/teachers/presentation/managers/cubits/teachers_cubit.dart';
import '/features/teachers/presentation/view/teachers_view.dart';
import '/features/home/data/repositories/class_schedule/class_schedule_repositories_implementation.dart';
import '/features/home/presentation/managers/cubits/class_schedule/class_schedule_cubit.dart';
import '/features/work_hours_to_all_students/data/repositories/schedule_to_all_student_repositories_implementation.dart';
import '/features/work_hours_to_all_students/presentation/managers/cubits/schedule_to_all_student_cubit.dart';
import '/features/work_hours_to_all_students/presentation/view/work_hours_to_all_students_view.dart';
import '/features/work_hours_to_batch/data/repositories/schedule_to_batch_repositories_implementation.dart';
import '/features/work_hours_to_batch/presentation/managers/cubits/schedule_to_batch_cubit.dart';
import '/features/work_hours_to_batch/presentation/view/work_hours_to_batch_view.dart';
import '/features/work_hours_to_student/data/repositories/schedule_to_student_repositories_implementation.dart';
import '/features/work_hours_to_student/presentation/managers/cubits/schedule_to_student_cubit.dart';
import '/features/work_hours_to_student/presentation/view/work_hours_to_student_view.dart';

abstract class AppRouter {
  static GoRouter goRouter = GoRouter(
    routes: [
      GoRoute(
        path: kSplashViewRouter,
        builder: (context, state) => const SplashView(),
      ),
      GoRoute(
        path: kAuthViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => AuthCubit(
            authRepositoriesImplementation: getIt
                .get<AuthRepositoriesImplementation>(),
          ),
          child: const AuthView(),
        ),
      ),
      GoRoute(
        path: kHomeViewRouter,
        builder: (context, state) => MultiBlocProvider(
          providers: [
            BlocProvider(
              create: (context) => ExamsCubit(
                examsRepositoriesImplementation: getIt
                    .get<ExamsRepositoriesImplementation>(),
              ),
            ),
            BlocProvider(
              create: (context) => ClassScheduleCubit(
                classScheduleRepositoryImplementation: getIt
                    .get<ClassScheduleRepositoryImplementation>(),
              )..getTodaySchedule(),
            ),
            BlocProvider(
              create: (context) => TotalStudentsCubit(
                totalStudentsRepositoriesImplementation: getIt
                    .get<TotalStudentsRepositoriesImplementation>(),
              )..getTotalStudents(),
            ),
            BlocProvider(
              create: (context) => AcademicBranchesCoursesDetailsCubit(
                academicBranchesRepositoriesImplementation: getIt
                    .get<
                      AcademicBranchesCoursesDetailsRepositoriesImplementation
                    >(),
              )..getBranches(genderType: 'all'),
            ),
            BlocProvider(
              create: (context) => AcademicBranchesCubit(
                academicBranchesRepositoriesImplementation: getIt
                    .get<AcademicBranchesRepositoriesImplementation>(),
              )..getBranches(),
            ),
            BlocProvider(
              create: (context) => AuthCubit(
                authRepositoriesImplementation: getIt
                    .get<AuthRepositoriesImplementation>(),
              )..loginMethod(uniqueId: 'OAD-00001', password: 'password123'),
            ),
            BlocProvider(
              create: (context) => InstituteBranchCubit(
                instituteBranchRepositoriesImplementation: getIt
                    .get<InstituteBranchRepositoriesImplementation>(),
              )..getInstituteBranches(),
            ),
            BlocProvider(
              create: (context) => BatchAverageCubit(
                batchAverageRepositoriesImplementation: getIt
                    .get<BatchAverageRepositoriesImplementation>(),
              )..getBatchAverages(),
            ),
            BlocProvider(
              create: (context) => TeachersCubit(
                teachersRepositoriesImplementation: getIt
                    .get<TeachersRepositoriesImplementation>(),
              )..getAllTeachers(),
            ),
            BlocProvider(
              create: (context) => EmployeeCubit(
                employeesRepositoriesImplementation: getIt
                    .get<EmployeesRepositoriesImplementation>(),
              ),
            ),
            BlocProvider(
              create: (context) => EmployeePhotoCubit(
                photoEmployeeRepositoriesImplementation: getIt
                    .get<PhotoEmployeeRepositoriesImplementation>(),
              ),
            ),
            BlocProvider(
              create: (context) => LogOutCubit(
                logOutRepositoriesImplementation: getIt
                    .get<LogOutRepositoriesImplementation>(),
              ),
            ),
          ],
          child: const HomeView(),
        ),
      ),
      GoRoute(
        path: kExamViewToHoleAcademicRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => ExamsCubit(
            examsRepositoriesImplementation: getIt
                .get<ExamsRepositoriesImplementation>(),
          ),
          child: const ExamView(),
        ),
      ),
      GoRoute(
        path: kWorkHoursViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => ScheduleToAllStudentCubit(
            scheduleToBatchRepositoriesImplementation: getIt
                .get<ScheduleToAllStudentRepositoriesImplementation>(),
          )..getSchedule(),
          child: const WorkHoursView(),
        ),
      ),
      GoRoute(
        path: kProfileViewRouter,
        builder: (context, state) => MultiBlocProvider(
          providers: [
            BlocProvider(
              create: (context) => EmployeeCubit(
                employeesRepositoriesImplementation: getIt
                    .get<EmployeesRepositoriesImplementation>(),
              ),
            ),
            BlocProvider(
              create: (context) => EmployeePhotoCubit(
                photoEmployeeRepositoriesImplementation: getIt
                    .get<PhotoEmployeeRepositoriesImplementation>(),
              ),
            ),
            BlocProvider(
              create: (context) => LogOutCubit(
                logOutRepositoriesImplementation: getIt
                    .get<LogOutRepositoriesImplementation>(),
              ),
            ),
          ],
          child: const ProfileView(),
        ),
      ),
      GoRoute(
        path: kSearchViewRouter,
        builder: (context, state) => const SearchView(),
      ),
      GoRoute(
        path: kQRViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => DoorSessionCubit(
            doorSessionRepositoryImplementation: getIt
                .get<DoorSessionRepositoriesImplementation>(),
          )..autoGenerateSession(),
          child: const QRView(),
        ),
      ),
      GoRoute(
        path: kScanViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => ScanQrCubit(
            scanQrRepositoryImplementation: getIt
                .get<ScanQrRepositoryImplementation>(),
          ),
          child: const ScanView(),
        ),
      ),
      GoRoute(
        path: kCoursesViewRouter,
        builder: (context, state) => MultiBlocProvider(
          providers: [
            BlocProvider(
              create: (context) => TotalStudentsCubit(
                totalStudentsRepositoriesImplementation: getIt
                    .get<TotalStudentsRepositoriesImplementation>(),
              )..getTotalStudents(),
            ),
            BlocProvider(
              create: (context) => AcademicBranchesCubit(
                academicBranchesRepositoriesImplementation: getIt
                    .get<AcademicBranchesRepositoriesImplementation>(),
              )..getBranches(),
            ),
          ],
          child: const CoursesView(),
        ),
      ),
      GoRoute(
        path: kCoursesDetailsViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => AcademicBranchesCoursesDetailsCubit(
            academicBranchesRepositoriesImplementation: getIt
                .get<
                  AcademicBranchesCoursesDetailsRepositoriesImplementation
                >(),
          )..getBranches(genderType: 'all'),
          child: const CoursesDetailsView(),
        ),
      ),
      GoRoute(
        path: kClassViewRouter,
        builder: (context, state) => MultiBlocProvider(
          providers: [
            BlocProvider(
              create: (context) => BatchStudentsCubit(
                batchStudentsRepositoriesImplementation: getIt
                    .get<BatchStudentsRepositoriesImplementation>(),
              )..getStudents(),
            ),
            BlocProvider(
              create: (context) => SendMTNMessageCubit(
                sendMTNMessageRepositoriesImplementation: getIt
                    .get<SendMTNMessageRepositoriesImplementation>(),
              ),
            ),
          ],
          child: const ClassView(),
        ),
      ),
      GoRoute(
        path: kDetailsStudentViewRouter,
        builder: (context, state) => MultiBlocProvider(
          providers: [
            BlocProvider(
              create: (context) => DetailsStudentsCubit(
                detailsStudentsRepositoriesImplementation: getIt
                    .get<DetailsStudentsRepositoriesImplementation>(),
              )..getDetailsStudentById(),
            ),
            BlocProvider(
              create: (context) => FinancialSummaryCubit(
                financialSummaryRepositoriesImplementation: getIt
                    .get<FinancialSummaryRepositoriesImplementation>(),
              )..getStudentFinancialSummary(),
            ),
            BlocProvider(
              create: (context) => MarkStudentsCubit(
                markStudentsRepositoriesImplementation: getIt
                    .get<MarkStudentsRepositoriesImplementation>(),
              )..getLastTwoWeeksExams(),
            ),
            BlocProvider(
              create: (context) => MonthlyEvaluationCubit(
                repository: getIt
                    .get<MonthlyEvaluationRepositoryImplementation>(),
              )..getMonthlyEvaluations(),
            ),
          ],
          child: const DetailsStudentView(),
        ),
      ),
      GoRoute(
        path: kNotificationsViewRouter,
        builder: (context, state) => const NotificationsView(),
      ),
      GoRoute(
        path: kPaymentsViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => FinancialSummaryCubit(
            financialSummaryRepositoriesImplementation: getIt
                .get<FinancialSummaryRepositoriesImplementation>(),
          )..getStudentFinancialSummary(),
          child: const PaymentsView(),
        ),
      ),
      GoRoute(
        path: kExamsView2Router,
        builder: (context, state) => BlocProvider(
          create: (context) => MarkStudentsCubit(
            markStudentsRepositoriesImplementation: getIt
                .get<MarkStudentsRepositoriesImplementation>(),
          )..getLastTwoWeeksExams(),
          child: const ExamsView2(),
        ),
      ),
      GoRoute(
        path: kFilterExamsView2Router,
        builder: (context, state) => MultiBlocProvider(
          providers: [
            BlocProvider(
              create: (context) => SubjectsCubit(
                subjectsRepositoriesImplementation: getIt
                    .get<SubjectsRepositoriesImplementation>(),
              )..getSubjectsByAcademicBranch(),
            ),
          ],
          child: const FilterExams2View(),
        ),
      ),
      GoRoute(
        path: kExamsToStudentViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => StudentExamsCubit(
            repository: getIt.get<StudentExamsRepositoryImplementation>(),
          )..getTodayAndWeekExams(),
          child: const ExamsToStudentView(),
        ),
      ),
      GoRoute(
        path: kWorkHoursToStudentViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => ScheduleToStudentCubit(
            scheduleToStudentRepositoriesImplementation: getIt
                .get<ScheduleToStudentRepositoriesImplementation>(),
          )..getSchedule(),
          child: const WorkHoursToStudentView(),
        ),
      ),
      GoRoute(
        path: kWorkHoursToBatchViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => ScheduleToBatchCubit(
            scheduleToBatchRepositoriesImplementation: getIt
                .get<ScheduleToBatchRepositoriesImplementation>(),
          )..getSchedule(),
          child: const WorkHoursToBatchView(),
        ),
      ),
      GoRoute(
        path: kAttendanceViewRouter,
        builder: (context, state) => MultiBlocProvider(
          providers: [
            BlocProvider(
              create: (context) => AttendanceCubit(
                attendanceRepositoryImplementation: getIt
                    .get<AttendanceRepositoriesImplementation>(),
              )..getAttendanceLog(),
            ),
            BlocProvider(
              create: (context) => DetailsStudentsCubit(
                detailsStudentsRepositoriesImplementation: getIt
                    .get<DetailsStudentsRepositoriesImplementation>(),
              )..getDetailsStudentById(),
            ),
          ],
          child: const AttendanceView(),
        ),
      ),
      GoRoute(
        path: kFilterAttendanceViewRouter,
        builder: (context, state) => const FilterAttendanceView(),
      ),
      GoRoute(
        path: kMarkToBatchViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => LastAndCurrentWeeksExamsToBatchCubit(
            lastAndCurrentWeekExamsToBatchRepositoriesImplementation: getIt
                .get<
                  LastAndCurrentWeekExamsToBatchRepositoriesImplementation
                >(),
          )..getLastTwoWeeksExams(),
          child: const MarkToBatchView(),
        ),
      ),
      GoRoute(
        path: kDetailsMarkToBatchViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => ExamsResultToBatchCubit(
            examsResultToBatchRepositoriesImplementation: getIt
                .get<ExamsResultToBatchRepositoriesImplementation>(),
          )..getExamsResults(),
          child: const DetailsMarkToBatchView(),
        ),
      ),
      GoRoute(
        path: kTeachersViewRouter,
        builder: (context, state) => BlocProvider(
          create: (context) => TeachersCubit(
            teachersRepositoriesImplementation: getIt
                .get<TeachersRepositoriesImplementation>(),
          )..getAllTeachers(),
          child: const TeachersView(),
        ),
      ),
    ],
  );
}
