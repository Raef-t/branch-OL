import '/features/work_hours_to_student/presentation/managers/models/schedule_to_student_model.dart';

abstract class ScheduleToStudentState {}

class ScheduleToStudentInitialState extends ScheduleToStudentState {}

class ScheduleToStudentLoadingState extends ScheduleToStudentState {}

class ScheduleToStudentSuccessState extends ScheduleToStudentState {
  final ScheduleToStudentModel scheduleToStudentModelInCubit;
  ScheduleToStudentSuccessState({required this.scheduleToStudentModelInCubit});
}

class ScheduleToStudentFailureState extends ScheduleToStudentState {
  final String errorMessageInCubit;
  ScheduleToStudentFailureState({required this.errorMessageInCubit});
}
