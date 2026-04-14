import '/features/details_students/presentation/managers/models/details_students/details_students_model.dart';

abstract class DetailsStudentsState {}

class DetailsStudentInitialState extends DetailsStudentsState {}

class DetailsStudentLoadingState extends DetailsStudentsState {}

class DetailsStudentSuccessState extends DetailsStudentsState {
  final DetailsStudentsModel detailsStudentsModelInCubit;
  DetailsStudentSuccessState({required this.detailsStudentsModelInCubit});
}

class DetailsStudentFailureState extends DetailsStudentsState {
  final String errorMessageInCubit;
  DetailsStudentFailureState({required this.errorMessageInCubit});
}
