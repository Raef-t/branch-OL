import '/features/home/presentation/managers/models/institute_branch/institute_branch_model.dart';

abstract class InstituteBranchState {}

class InstituteBranchInitialState extends InstituteBranchState {}

class InstituteBranchLoadingState extends InstituteBranchState {}

class InstituteBranchSuccessState extends InstituteBranchState {
  final List<InstituteBranchModel> listOfInstituteBranchModelInCubit;
  final InstituteBranchModel? selectedInstituteBranchModelInCubit;
  InstituteBranchSuccessState({
    required this.listOfInstituteBranchModelInCubit,
    this.selectedInstituteBranchModelInCubit,
  });
}

class InstituteBranchFailureState extends InstituteBranchState {
  final String errorMessageInCubit;
  InstituteBranchFailureState({required this.errorMessageInCubit});
}
