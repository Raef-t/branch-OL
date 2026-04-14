import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/details_marks_to_batch/data/repositories/exams_result_to_batch_repositories_implementation.dart';
import '/features/details_marks_to_batch/presentation/managers/cubits/exams_result_to_batch_state.dart';

class ExamsResultToBatchCubit extends Cubit<ExamsResultToBatchState> {
  ExamsResultToBatchCubit({
    required this.examsResultToBatchRepositoriesImplementation,
  }) : super(ExamsResultToBatchInitialState());
  final ExamsResultToBatchRepositoriesImplementation
  examsResultToBatchRepositoriesImplementation;
  Future<void> getExamsResults() async {
    emit(ExamsResultToBatchLoadingState());
    final subjectId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keySubjectToBatchInSharedPreferences,
    );
    final result = await examsResultToBatchRepositoriesImplementation
        .getExamsResults(subjectId: subjectId ?? 0);
    result.fold(
      (failure) => emit(
        ExamsResultToBatchFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (results) => emit(
        ExamsResultToBatchSuccessState(
          listOfExamsResultToBatchModelInCubit: results,
        ),
      ),
    );
  }
}
