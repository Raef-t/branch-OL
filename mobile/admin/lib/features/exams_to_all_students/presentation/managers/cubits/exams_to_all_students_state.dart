import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';

abstract class ExamsState {}

class ExamsInitialState extends ExamsState {}

class ExamsLoadingState extends ExamsState {}

class ExamsSuccessState extends ExamsState {
  final List<ExamsModel> examsListInCubit;
  ExamsSuccessState({required this.examsListInCubit});
}

class ExamsFailureState extends ExamsState {
  final String errorMessageInCubit;
  ExamsFailureState({required this.errorMessageInCubit});
}
