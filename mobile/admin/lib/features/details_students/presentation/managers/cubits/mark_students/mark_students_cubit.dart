import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/details_students/data/repositories/mark_students/mark_students_repositories_implementation.dart';
import '/features/details_students/presentation/managers/cubits/mark_students/mark_students_state.dart';

class MarkStudentsCubit extends Cubit<MarkStudentsState> {
  MarkStudentsCubit({required this.markStudentsRepositoriesImplementation})
    : super(MarkStudentsInitialState());
  final MarkStudentsRepositoriesImplementation
  markStudentsRepositoriesImplementation;
  Future<void> getLastTwoWeeksExams() async {
    emit(MarkStudentsLoadingState());
    final studentId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyStudentIdInSharedPreferences,
    );
    final result = await markStudentsRepositoriesImplementation
        .getLastTwoWeeksExams(studentId: studentId ?? 0);
    result.fold(
      (failure) {
        emit(
          MarkStudentsFailureState(
            errorMessageInCubit: failure.errorMessageInFailureError,
          ),
        );
      },
      (marks) {
        emit(MarkStudentsSuccessState(markTwoWeeksModelInCubit: marks));
      },
    );
  }
}
