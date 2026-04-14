import '/features/work_hours_to_batch/presentation/managers/models/schedule_to_batch_model.dart';

abstract class ScheduleToBatchState {}

class ScheduleToBatchInitialState extends ScheduleToBatchState {}

class ScheduleToBatchLoadingState extends ScheduleToBatchState {}

class ScheduleToBatchSuccessState extends ScheduleToBatchState {
  final ScheduleToBatchModel scheduleToBatchModelInCubit;
  ScheduleToBatchSuccessState({required this.scheduleToBatchModelInCubit});
}

class ScheduleToBatchFailureState extends ScheduleToBatchState {
  final String errorMessageInCubit;
  ScheduleToBatchFailureState({required this.errorMessageInCubit});
}
