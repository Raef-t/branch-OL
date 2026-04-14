import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';

abstract class AcademicBranchesCoursesDetailsState {}

class AcademicBranchesInitialCoursesDetailsState
    extends AcademicBranchesCoursesDetailsState {}

class AcademicBranchesLoadingCoursesDetailsState
    extends AcademicBranchesCoursesDetailsState {}

class AcademicBranchesSuccessCoursesDetailsState
    extends AcademicBranchesCoursesDetailsState {
  final List<AcademicBranchesCoursesDetailsModel>
  listOfAcademicBranchesModelInCubit;
  AcademicBranchesSuccessCoursesDetailsState({
    required this.listOfAcademicBranchesModelInCubit,
  });
}

class AcademicBranchesFailureCoursesDetailsState
    extends AcademicBranchesCoursesDetailsState {
  final String errorMessageInCubit;
  AcademicBranchesFailureCoursesDetailsState({
    required this.errorMessageInCubit,
  });
}
