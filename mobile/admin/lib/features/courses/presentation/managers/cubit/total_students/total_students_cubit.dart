import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/courses/data/repositories/total_students/total_students_repositories_implementation.dart';
import '/features/courses/presentation/managers/cubit/total_students/total_students_state.dart';

class TotalStudentsCubit extends Cubit<TotalStudentsState> {
  TotalStudentsCubit({required this.totalStudentsRepositoriesImplementation})
    : super(TotalStudentsInitialState());
  final TotalStudentsRepositoriesImplementation
  totalStudentsRepositoriesImplementation;
  Future<void> getTotalStudents() async {
    emit(TotalStudentsLoadingState());
    final branchId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyInstituteBranchIdInSharedPreferences,
    );
    final result = await totalStudentsRepositoriesImplementation
        .getTotalStudents(branchId: branchId ?? 1);
    result.fold(
      (failure) => emit(
        TotalStudentsFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (totalStudents) => emit(
        TotalStudentsSuccessState(totalStudentsModelInCubit: totalStudents),
      ),
    );
  }
}
