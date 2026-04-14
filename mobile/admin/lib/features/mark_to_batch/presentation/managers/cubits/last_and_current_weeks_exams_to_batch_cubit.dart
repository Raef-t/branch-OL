import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/mark_to_batch/data/repositories/last_and_current_week_exams_to_batch_repositories_implementation.dart';
import '/features/mark_to_batch/presentation/managers/cubits/last_and_current_weeks_exams_to_batch_state.dart';

class LastAndCurrentWeeksExamsToBatchCubit
    extends Cubit<LastAndCurrentWeeksExamsToBatchState> {
  final LastAndCurrentWeekExamsToBatchRepositoriesImplementation
  lastAndCurrentWeekExamsToBatchRepositoriesImplementation;
  LastAndCurrentWeeksExamsToBatchCubit({
    required this.lastAndCurrentWeekExamsToBatchRepositoriesImplementation,
  }) : super(LastAndCurrentWeeksExamsToBatchInitialState());
  Future<void> getLastTwoWeeksExams() async {
    emit(LastAndCurrentWeeksExamsToBatchLoadingState());
    final batchId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyBatchIdInSharedPreferences,
    );
    final result =
        await lastAndCurrentWeekExamsToBatchRepositoriesImplementation
            .getLastAndCurrentTwoWeeksExams(batchId: batchId ?? 1);
    result.fold(
      (failure) {
        emit(
          LastAndCurrentWeeksExamsToBatchFailureState(
            errorMessageInCubit: failure.errorMessageInFailureError,
          ),
        );
      },
      (data) {
        emit(
          LastAndCurrentWeeksExamsToBatchSuccessState(
            currentAndLastWeeksExamsToBatchModelInCubit: data,
          ),
        );
      },
    );
  }
}
