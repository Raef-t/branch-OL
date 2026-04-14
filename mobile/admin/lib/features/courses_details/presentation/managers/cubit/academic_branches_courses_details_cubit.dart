import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/courses_details/data/repositories/academic_branches_repositories_courses_details_implementation.dart';
import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';
import 'academic_branches_courses_details_state.dart';

class AcademicBranchesCoursesDetailsCubit
    extends Cubit<AcademicBranchesCoursesDetailsState> {
  final AcademicBranchesCoursesDetailsRepositoriesImplementation
  academicBranchesRepositoriesImplementation;
  AcademicBranchesCoursesDetailsCubit({
    required this.academicBranchesRepositoriesImplementation,
  }) : super(AcademicBranchesInitialCoursesDetailsState());
  AcademicBranchesCoursesDetailsModel? selectedBranch;
  Future<void> getBranches({required String genderType}) async {
    emit(AcademicBranchesLoadingCoursesDetailsState());
    final instituteBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyInstituteBranchIdInSharedPreferences,
        );
    final result = await academicBranchesRepositoriesImplementation
        .getAcademicBranches(
          genderType: genderType,
          instituteBranchId: instituteBranchId ?? 1,
        );
    result.fold(
      (failure) => emit(
        AcademicBranchesFailureCoursesDetailsState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (academicBranches) {
        if (academicBranches.isNotEmpty && selectedBranch == null) {
          selectedBranch = academicBranches.first;
        }
        emit(
          AcademicBranchesSuccessCoursesDetailsState(
            listOfAcademicBranchesModelInCubit: academicBranches,
          ),
        );
      },
    );
  }

  void selectBranch(AcademicBranchesCoursesDetailsModel branch) {
    selectedBranch = branch;
    if (state is AcademicBranchesSuccessCoursesDetailsState) {
      emit(
        AcademicBranchesSuccessCoursesDetailsState(
          listOfAcademicBranchesModelInCubit:
              (state as AcademicBranchesSuccessCoursesDetailsState)
                  .listOfAcademicBranchesModelInCubit,
        ),
      );
    }
  }
}
