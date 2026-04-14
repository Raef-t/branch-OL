import 'package:dio/dio.dart';
import 'package:get_it/get_it.dart';
import '/core/helpers/build_base_options_to_auth_helper.dart';
import '/core/helpers/build_base_options_to_create_qr_helper.dart';
import '/core/helpers/build_base_options_to_m_t_n_helper.dart';
import '/core/helpers/build_dio_with_token_to_hole_app_helper.dart';
import '/features/attendance/data/repositories/attendance_repositories_implementation.dart';
import '/features/attendance/data/services/attendance_service.dart';
import '/features/auth/data/repositories/auth_repositories_implementation.dart';
import '/features/auth/data/services/auth_service.dart';
import '/features/class/data/repositories/batch_students/batch_students_repositories_implementation.dart';
import '/features/class/data/repositories/send_m_t_n_message/send_m_t_n_message_repositories_implementation.dart';
import '/features/class/data/services/batch_students/batch_students_service.dart';
import '/features/class/data/services/send_m_t_n_message/send_m_t_n_message_service.dart';
import '/features/courses/data/repositories/academic_branches/academic_branches_repositories_implementation.dart';
import '/features/courses/data/repositories/total_students/total_students_repositories_implementation.dart';
import '/features/courses/data/services/academic_branches/academic_branches_service.dart';
import '/features/courses/data/services/total_students/total_students_service.dart';
import '/features/courses_details/data/repositories/academic_branches_repositories_courses_details_implementation.dart';
import '/features/courses_details/data/services/academic_branches_courses_details_service.dart';
import '/features/details_marks_to_batch/data/repositories/exams_result_to_batch_repositories_implementation.dart';
import '/features/details_marks_to_batch/data/services/exams_result_to_batch_service.dart';
import '/features/details_students/data/repositories/details_students/details_students_repositories_implementation.dart';
import '/features/details_students/data/repositories/financial_summary/financial_summary_repositories_implementation.dart';
import '/features/details_students/data/repositories/mark_students/mark_students_repositories_implementation.dart';
import '/features/details_students/data/repositories/monthly_evaluations/monthly_evaluations_repositories_implementation.dart';
import '/features/details_students/data/services/details_students/details_students_service.dart';
import '/features/details_students/data/services/financial_summary/financial_summary_service.dart';
import '/features/details_students/data/services/mark_students/mark_students_service.dart';
import '/features/details_students/data/services/monthly_evaluations/monthly_evaluations_service.dart';
import '/features/exams_to_all_students/data/repositories/exams_to_all_students_repositories_implementation.dart';
import '/features/exams_to_all_students/data/services/exams_to_all_students_service.dart';
import '/features/exams_to_student/data/repositories/student_exams_repositories_implementation.dart';
import '/features/exams_to_student/data/services/student_exams_service.dart';
import '/features/filter_exams/data/repositories/subjects/subjects_repositories_implementation.dart';
import '/features/filter_exams/data/services/subjects/subjects_service.dart';
import '/features/home/data/repositories/batch_average/batch_average_repositories_implementation.dart';
import '/features/home/data/repositories/institute_branch/institute_branch_repositories_implementation.dart';
import '/features/home/data/services/batch_average/batch_average_service.dart';
import '/features/home/data/services/institute_branch/institute_branch_service.dart';
import '/features/mark_to_batch/data/repositories/last_and_current_week_exams_to_batch_repositories_implementation.dart';
import '/features/mark_to_batch/data/services/last_and_current_week_exams_to_batch_service.dart';
import '/features/profile/data/repositories/edit_first_and_last_name_employee/employee_repositories_implementation.dart';
import '/features/profile/data/repositories/edit_photo_employee/photo_employee_repositories_implementation.dart';
import '/features/profile/data/repositories/logout/log_out_repositories_implementation.dart';
import '/features/profile/data/services/edit_first_and_last_name_employee/employee_service.dart';
import '/features/profile/data/services/edit_photo_employee/photo_employee_service.dart';
import '/features/profile/data/services/logout/log_out_service.dart';
import '/features/q_r/data/repositories/door_session_repositories_implementation.dart';
import '/features/q_r/data/services/door_session_service.dart';
import '/features/scan/data/repositories/scan_qr_repositories_implementation.dart';
import '/features/scan/data/services/scan_qr_service.dart';
import '/features/teachers/data/repositories/teachers_repositories_implementation.dart';
import '/features/teachers/data/services/teachers_service.dart';
import '/features/home/data/repositories/class_schedule/class_schedule_repositories_implementation.dart';
import '/features/home/data/services/class_schedule/class_schedule_service.dart';
import '/features/work_hours_to_all_students/data/repositories/schedule_to_all_student_repositories_implementation.dart';
import '/features/work_hours_to_all_students/data/services/schedule_to_all_student_service.dart';
import '/features/work_hours_to_batch/data/repositories/schedule_to_batch_repositories_implementation.dart';
import '/features/work_hours_to_batch/data/services/schedule_to_batch_service.dart';
import '/features/work_hours_to_student/data/repositories/schedule_to_student_repositories_implementation.dart';
import '/features/work_hours_to_student/data/services/schedule_to_student_service.dart';

final getIt = GetIt.instance;

void setUpServiceLocator() {
  getIt.registerSingleton<Dio>(buildDioWithTokenToHoleAppHelper());
  getIt.registerSingleton<ExamsRepositoriesImplementation>(
    ExamsRepositoriesImplementation(
      examsService: ExamsService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<ClassScheduleRepositoryImplementation>(
    ClassScheduleRepositoryImplementation(
      classScheduleService: ClassScheduleService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<TotalStudentsRepositoriesImplementation>(
    TotalStudentsRepositoriesImplementation(
      totalStudentsService: TotalStudentsService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<AcademicBranchesRepositoriesImplementation>(
    AcademicBranchesRepositoriesImplementation(
      academicBranchesService: AcademicBranchesService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<
    AcademicBranchesCoursesDetailsRepositoriesImplementation
  >(
    AcademicBranchesCoursesDetailsRepositoriesImplementation(
      academicBranchesService: AcademicBranchesCoursesDetailsService(
        dio: getIt.get<Dio>(),
      ),
    ),
  );
  getIt.registerSingleton<BatchStudentsRepositoriesImplementation>(
    BatchStudentsRepositoriesImplementation(
      batchStudentsService: BatchStudentsService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<DetailsStudentsRepositoriesImplementation>(
    DetailsStudentsRepositoriesImplementation(
      detailsStudentsService: DetailsStudentsService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<FinancialSummaryRepositoriesImplementation>(
    FinancialSummaryRepositoriesImplementation(
      financialSummaryService: FinancialSummaryService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<MarkStudentsRepositoriesImplementation>(
    MarkStudentsRepositoriesImplementation(
      markStudentsService: MarkStudentsService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<SubjectsRepositoriesImplementation>(
    SubjectsRepositoriesImplementation(
      subjectsService: SubjectsService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<InstituteBranchRepositoriesImplementation>(
    InstituteBranchRepositoriesImplementation(
      instituteBranchService: InstituteBranchService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<BatchAverageRepositoriesImplementation>(
    BatchAverageRepositoriesImplementation(
      batchAverageService: BatchAverageService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<SendMTNMessageRepositoriesImplementation>(
    SendMTNMessageRepositoriesImplementation(
      sendMTNMessageService: SendMTNMessageService(
        dio: Dio(buildBaseOptionsToMTNHelper()),
      ),
    ),
  );
  getIt.registerSingleton<AuthRepositoriesImplementation>(
    AuthRepositoriesImplementation(
      authService: AuthService(dio: Dio(buildBaseOptionsToAuthHelper())),
    ),
  );
  getIt.registerSingleton<TeachersRepositoriesImplementation>(
    TeachersRepositoriesImplementation(
      teachersService: TeachersService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<
    LastAndCurrentWeekExamsToBatchRepositoriesImplementation
  >(
    LastAndCurrentWeekExamsToBatchRepositoriesImplementation(
      lastAndCurrentWeekExamsToBatchService:
          LastAndCurrentWeekExamsToBatchService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<ExamsResultToBatchRepositoriesImplementation>(
    ExamsResultToBatchRepositoriesImplementation(
      examsResultToBatchService: ExamsResultToBatchService(
        dio: getIt.get<Dio>(),
      ),
    ),
  );
  getIt.registerSingleton<AttendanceRepositoriesImplementation>(
    AttendanceRepositoriesImplementation(
      attendanceService: AttendanceService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<StudentExamsRepositoryImplementation>(
    StudentExamsRepositoryImplementation(
      studentExamsService: StudentExamsService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<ScheduleToBatchRepositoriesImplementation>(
    ScheduleToBatchRepositoriesImplementation(
      scheduleToBatchService: ScheduleToBatchService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<ScheduleToStudentRepositoriesImplementation>(
    ScheduleToStudentRepositoriesImplementation(
      scheduleToStudentService: ScheduleToStudentService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton<ScheduleToAllStudentRepositoriesImplementation>(
    ScheduleToAllStudentRepositoriesImplementation(
      scheduleToBatchService: ScheduleToAllStudentService(
        dio: getIt.get<Dio>(),
      ),
    ),
  );
  getIt.registerSingleton<EmployeesRepositoriesImplementation>(
    EmployeesRepositoriesImplementation(
      employeesService: EmployeesService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton(
    PhotoEmployeeRepositoriesImplementation(
      photoEmployeeService: PhotoEmployeeService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton(
    LogOutRepositoriesImplementation(
      logOutService: LogOutService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton(
    DoorSessionRepositoriesImplementation(
      doorSessionService: DoorSessionService(
        dio: Dio(buildBaseOptionsToCreateQrHelper()),
      ),
    ),
  );
  getIt.registerSingleton(
    ScanQrRepositoryImplementation(
      scanQrService: ScanQrService(dio: getIt.get<Dio>()),
    ),
  );
  getIt.registerSingleton(
    MonthlyEvaluationRepositoryImplementation(
      service: MonthlyEvaluationService(dio: getIt.get<Dio>()),
    ),
  );
}
