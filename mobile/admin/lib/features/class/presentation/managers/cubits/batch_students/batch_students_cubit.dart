import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/class/data/repositories/batch_students/batch_students_repositories_implementation.dart';
import '/features/class/presentation/managers/cubits/batch_students/batch_students_state.dart';

class BatchStudentsCubit extends Cubit<BatchStudentsState> {
  final BatchStudentsRepositoriesImplementation
  batchStudentsRepositoriesImplementation;
  BatchStudentsCubit({required this.batchStudentsRepositoriesImplementation})
    : super(BatchStudentsInitialState());
  Future<void> getStudents() async {
    emit(BatchStudentsLoadingState());
    final int? batchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyBatchIdInSharedPreferences,
        );
    final result = await batchStudentsRepositoriesImplementation
        .getStudentsByBatchId(batchId: batchId ?? 0);
    result.fold(
      (failure) => emit(
        BatchStudentsFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (batchStudents) => emit(
        BatchStudentsSuccessState(
          listOfBatchStudentsModelInCubit: batchStudents,
        ),
      ),
    );
  }
}
