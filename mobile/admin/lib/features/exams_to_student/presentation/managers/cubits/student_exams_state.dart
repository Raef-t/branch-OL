import '/features/exams_to_student/presentation/managers/models/current_and_last_model.dart';

abstract class StudentExamsState {}

class StudentExamsInitialState extends StudentExamsState {}

class StudentExamsLoadingState extends StudentExamsState {}

class StudentExamsSuccessState extends StudentExamsState {
  final CurrentAndLastModel data;

  StudentExamsSuccessState({required this.data});
}

class StudentExamsFailureState extends StudentExamsState {
  final String errorMessage;

  StudentExamsFailureState({required this.errorMessage});
}
