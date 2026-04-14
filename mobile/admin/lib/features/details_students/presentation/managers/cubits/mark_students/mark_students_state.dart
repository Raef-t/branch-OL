import '/features/details_students/presentation/managers/models/marks_student/mark_two_weeks_model.dart';

abstract class MarkStudentsState {}

class MarkStudentsInitialState extends MarkStudentsState {}

class MarkStudentsLoadingState extends MarkStudentsState {}

class MarkStudentsSuccessState extends MarkStudentsState {
  final MarkTwoWeeksModel markTwoWeeksModelInCubit;
  MarkStudentsSuccessState({required this.markTwoWeeksModelInCubit});
}

class MarkStudentsFailureState extends MarkStudentsState {
  final String errorMessageInCubit;
  MarkStudentsFailureState({required this.errorMessageInCubit});
}
