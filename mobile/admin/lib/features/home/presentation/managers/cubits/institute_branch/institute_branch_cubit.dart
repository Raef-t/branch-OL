import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/home/data/repositories/institute_branch/institute_branch_repositories_implementation.dart';
import '/features/home/presentation/managers/cubits/institute_branch/institute_branch_state.dart';
import '/features/home/presentation/managers/models/institute_branch/institute_branch_model.dart';

class InstituteBranchCubit extends Cubit<InstituteBranchState> {
  final InstituteBranchRepositoriesImplementation
  instituteBranchRepositoriesImplementation;
  InstituteBranchCubit({
    required this.instituteBranchRepositoriesImplementation,
  }) : super(InstituteBranchInitialState());

  List<InstituteBranchModel> _branches = [];

  Future<void> getInstituteBranches() async {
    emit(InstituteBranchLoadingState());
    final result = await instituteBranchRepositoriesImplementation
        .getInstituteBranches();
    result.fold(
      (failure) => emit(
        InstituteBranchFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (instituteBranches) {
        _branches = instituteBranches;
        emit(
          InstituteBranchSuccessState(
            listOfInstituteBranchModelInCubit: _branches,
          ),
        );
      },
    );
  }

  Future<void> selectBranch(InstituteBranchModel branch) async {
    await StoreParametersInSharedPreferences.saveIntParameter(
      intValue: branch.id ?? 1,
      key: keyInstituteBranchIdInSharedPreferences,
    );
    emit(
      InstituteBranchSuccessState(
        listOfInstituteBranchModelInCubit: _branches,
        selectedInstituteBranchModelInCubit: branch,
      ),
    );
  }
}
