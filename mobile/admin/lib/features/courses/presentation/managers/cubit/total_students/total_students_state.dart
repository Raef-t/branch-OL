import '/features/courses/presentation/managers/models/total_students/total_students_model.dart';

abstract class TotalStudentsState {}

class TotalStudentsInitialState extends TotalStudentsState {}

class TotalStudentsLoadingState extends TotalStudentsState {}

class TotalStudentsSuccessState extends TotalStudentsState {
  final TotalStudentsModel totalStudentsModelInCubit;
  TotalStudentsSuccessState({required this.totalStudentsModelInCubit});
}

class TotalStudentsFailureState extends TotalStudentsState {
  final String errorMessageInCubit;
  TotalStudentsFailureState({required this.errorMessageInCubit});
}
