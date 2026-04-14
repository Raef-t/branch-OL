import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/courses/data/repositories/academic_branches/academic_branches_repositories_implementation.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import 'academic_branches_state.dart';

class AcademicBranchesCubit extends Cubit<AcademicBranchesState> {
  final AcademicBranchesRepositoriesImplementation
  academicBranchesRepositoriesImplementation;
  AcademicBranchesCubit({
    required this.academicBranchesRepositoriesImplementation,
  }) : super(AcademicBranchesInitialState());
  AcademicBranchesToCoursesModel? selectedBranch;
  Future<void> getBranches() async {
    emit(AcademicBranchesLoadingState());
    final instituteBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyInstituteBranchIdInSharedPreferences,
        );
    final result = await academicBranchesRepositoriesImplementation
        .getAcademicBranches(
          genderType: 'all',
          instituteBranchId: instituteBranchId ?? 1,
        );
    result.fold(
      (failure) => emit(
        AcademicBranchesFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (academicBranches) => emit(
        AcademicBranchesSuccessState(
          listOfAcademicBranchesModelInCubit: academicBranches,
        ),
      ),
    );
  }
}
