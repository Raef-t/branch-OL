import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/exams_to_student/data/repositories/student_exams_repositories_implementation.dart';
import 'student_exams_state.dart';

class StudentExamsCubit extends Cubit<StudentExamsState> {
  final StudentExamsRepositoryImplementation repository;

  StudentExamsCubit({required this.repository})
    : super(StudentExamsInitialState());

  Future<void> getTodayAndWeekExams() async {
    emit(StudentExamsLoadingState());
    final studentId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyStudentIdInSharedPreferences,
    );
    final result = await repository.getTodayAndWeekExams(
      studentId: studentId ?? 1,
    );

    result.fold(
      (failure) => emit(
        StudentExamsFailureState(
          errorMessage: failure.errorMessageInFailureError,
        ),
      ),
      (data) => emit(StudentExamsSuccessState(data: data)),
    );
  }
}
