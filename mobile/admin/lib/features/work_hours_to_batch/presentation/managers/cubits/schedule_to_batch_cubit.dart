import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/work_hours_to_batch/data/repositories/schedule_to_batch_repositories_implementation.dart';
import '/features/work_hours_to_batch/presentation/managers/cubits/schedule_to_batch_state.dart';

class ScheduleToBatchCubit extends Cubit<ScheduleToBatchState> {
  final ScheduleToBatchRepositoriesImplementation
  scheduleToBatchRepositoriesImplementation;
  ScheduleToBatchCubit({
    required this.scheduleToBatchRepositoriesImplementation,
  }) : super(ScheduleToBatchInitialState());
  Future<void> getSchedule({String? day}) async {
    emit(ScheduleToBatchLoadingState());
    final batchId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyBatchIdInSharedPreferences,
    );
    final instituteBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyInstituteBranchIdInSharedPreferences,
        );
    final result = await scheduleToBatchRepositoriesImplementation.getSchedule(
      type: 'batch',
      id: (batchId == null || batchId == 0) ? 1 : batchId,
      day: day ?? 'today',
      instituteBranchId: (instituteBranchId == null || instituteBranchId == 0)
          ? 1
          : instituteBranchId,
    );
    result.fold(
      (failure) => emit(
        ScheduleToBatchFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (scheduleToBatchModel) => emit(
        ScheduleToBatchSuccessState(
          scheduleToBatchModelInCubit: scheduleToBatchModel,
        ),
      ),
    );
  }
}
