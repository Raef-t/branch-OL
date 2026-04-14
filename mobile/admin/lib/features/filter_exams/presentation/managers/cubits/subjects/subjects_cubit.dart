import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/filter_exams/data/repositories/subjects/subjects_repositories_implementation.dart';
import '/features/filter_exams/presentation/managers/cubits/subjects/subjects_state.dart';

class SubjectsCubit extends Cubit<SubjectsState> {
  SubjectsCubit({required this.subjectsRepositoriesImplementation})
    : super(SubjectsInitialState());
  final SubjectsRepositoriesImplementation subjectsRepositoriesImplementation;
  Future<void> getSubjectsByAcademicBranch() async {
    emit(SubjectsLoadingState());
    final academicBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyAcademicBranchIdInSharedPreferences,
        );
    final result = await subjectsRepositoriesImplementation
        .getSubjectsByAcademicBranch(academicBranchId: academicBranchId ?? 0);
    result.fold(
      (failure) {
        emit(
          SubjectsFailureState(
            errorMessageInCubit: failure.errorMessageInFailureError,
          ),
        );
      },
      (subjects) {
        emit(SubjectsSuccessState(listOfSubjectsModelInCubit: subjects));
      },
    );
  }
}
