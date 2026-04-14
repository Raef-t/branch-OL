import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';

abstract class AcademicBranchesState {}

class AcademicBranchesInitialState extends AcademicBranchesState {}

class AcademicBranchesLoadingState extends AcademicBranchesState {}

class AcademicBranchesSuccessState extends AcademicBranchesState {
  final List<AcademicBranchesToCoursesModel> listOfAcademicBranchesModelInCubit;
  AcademicBranchesSuccessState({
    required this.listOfAcademicBranchesModelInCubit,
  });
}

class AcademicBranchesFailureState extends AcademicBranchesState {
  final String errorMessageInCubit;
  AcademicBranchesFailureState({required this.errorMessageInCubit});
}
