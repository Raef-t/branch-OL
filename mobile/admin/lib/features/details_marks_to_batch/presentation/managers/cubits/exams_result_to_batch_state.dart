import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';

abstract class ExamsResultToBatchState {}

class ExamsResultToBatchInitialState extends ExamsResultToBatchState {}

class ExamsResultToBatchLoadingState extends ExamsResultToBatchState {}

class ExamsResultToBatchSuccessState extends ExamsResultToBatchState {
  final List<ExamsResultToBatchModel> listOfExamsResultToBatchModelInCubit;
  ExamsResultToBatchSuccessState({
    required this.listOfExamsResultToBatchModelInCubit,
  });
}

class ExamsResultToBatchFailureState extends ExamsResultToBatchState {
  final String errorMessageInCubit;
  ExamsResultToBatchFailureState({required this.errorMessageInCubit});
}
