import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/home/data/repositories/batch_average/batch_average_repositories_implementation.dart';
import '/features/home/presentation/managers/cubits/batch_average/batch_average_state.dart';

class BatchAverageCubit extends Cubit<BatchAveragesState> {
  BatchAverageCubit({required this.batchAverageRepositoriesImplementation})
    : super(BatchAveragesInitialState());
  final BatchAverageRepositoriesImplementation
  batchAverageRepositoriesImplementation;
  Future<void> getBatchAverages() async {
    emit(BatchAveragesLoadingState());
    final instituteBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyInstituteBranchIdInSharedPreferences,
        );
    final academicBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyAcademicBranchIdInSharedPreferences,
        );
    final result = await batchAverageRepositoriesImplementation
        .getBatchAverages(
          instituteBranchId: instituteBranchId ?? 1,
          academicBranchId: academicBranchId ?? 0,
        );
    result.fold(
      (failure) => emit(
        BatchAveragesFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (batchAverages) => emit(
        BatchAveragesSuccessState(
          listOfBatchAverageModelInCubit: batchAverages,
        ),
      ),
    );
  }
}
