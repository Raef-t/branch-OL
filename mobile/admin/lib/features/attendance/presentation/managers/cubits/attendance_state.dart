import '/features/attendance/presentation/managers/models/attendance_model.dart';

abstract class AttendanceState {}

class AttendanceInitialState extends AttendanceState {}

class AttendanceLoadingState extends AttendanceState {}

class AttendanceSuccessState extends AttendanceState {
  final List<AttendanceModel> listOfAttendaceModelInCubit;
  AttendanceSuccessState({required this.listOfAttendaceModelInCubit});
}

class AttendanceFailureState extends AttendanceState {
  final String errorMessageInCubit;
  AttendanceFailureState({required this.errorMessageInCubit});
}
