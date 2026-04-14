import '/features/filter_exams/presentation/managers/models/subjects/subjects_model.dart';

abstract class SubjectsState {}

class SubjectsInitialState extends SubjectsState {}

class SubjectsLoadingState extends SubjectsState {}

class SubjectsSuccessState extends SubjectsState {
  final List<SubjectsModel> listOfSubjectsModelInCubit;
  SubjectsSuccessState({required this.listOfSubjectsModelInCubit});
}

class SubjectsFailureState extends SubjectsState {
  final String errorMessageInCubit;
  SubjectsFailureState({required this.errorMessageInCubit});
}
