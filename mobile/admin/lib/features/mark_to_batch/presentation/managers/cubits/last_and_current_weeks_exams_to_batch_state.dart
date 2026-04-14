import '/features/mark_to_batch/presentation/managers/models/current_and_last_weeks_exams_to_batch_model.dart';

abstract class LastAndCurrentWeeksExamsToBatchState {}

class LastAndCurrentWeeksExamsToBatchInitialState
    extends LastAndCurrentWeeksExamsToBatchState {}

class LastAndCurrentWeeksExamsToBatchLoadingState
    extends LastAndCurrentWeeksExamsToBatchState {}

class LastAndCurrentWeeksExamsToBatchSuccessState
    extends LastAndCurrentWeeksExamsToBatchState {
  final CurrentAndLastWeeksExamsToBatchModel
  currentAndLastWeeksExamsToBatchModelInCubit;

  LastAndCurrentWeeksExamsToBatchSuccessState({
    required this.currentAndLastWeeksExamsToBatchModelInCubit,
  });
}

class LastAndCurrentWeeksExamsToBatchFailureState
    extends LastAndCurrentWeeksExamsToBatchState {
  final String errorMessageInCubit;
  LastAndCurrentWeeksExamsToBatchFailureState({
    required this.errorMessageInCubit,
  });
}
