import '/features/work_hours_to_all_students/presentation/managers/models/schedule_to_all_student_model.dart';

abstract class ScheduleToAllStudentState {}

class ScheduleToAllStudentInitialState extends ScheduleToAllStudentState {}

class ScheduleToAllStudentLoadingState extends ScheduleToAllStudentState {}

class ScheduleToAllStudentSuccessState extends ScheduleToAllStudentState {
  final ScheduleToAllStudentModel scheduleToBatchModelInCubit;
  ScheduleToAllStudentSuccessState({required this.scheduleToBatchModelInCubit});
}

class ScheduleToAllStudentFailureState extends ScheduleToAllStudentState {
  final String errorMessageInCubit;
  ScheduleToAllStudentFailureState({required this.errorMessageInCubit});
}
