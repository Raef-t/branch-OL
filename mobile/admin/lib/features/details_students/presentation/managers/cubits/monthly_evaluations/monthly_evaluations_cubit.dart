import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/details_students/data/repositories/monthly_evaluations/monthly_evaluations_repositories_implementation.dart';
import '/features/details_students/presentation/managers/cubits/monthly_evaluations/monthly_evaluations_state.dart';

class MonthlyEvaluationCubit extends Cubit<MonthlyEvaluationState> {
  final MonthlyEvaluationRepositoryImplementation repository;

  MonthlyEvaluationCubit({required this.repository})
    : super(MonthlyEvaluationInitial());
  Future<void> getMonthlyEvaluations() async {
    emit(MonthlyEvaluationLoading());
    final studentId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyStudentIdInSharedPreferences,
    );
    final result = await repository.getMonthlyEvaluations(
      studentId: studentId ?? 1,
    );
    result.fold(
      (failure) {
        emit(
          MonthlyEvaluationFailure(
            errorMessage: failure.errorMessageInFailureError,
          ),
        );
      },
      (data) {
        emit(MonthlyEvaluationSuccess(evaluations: data));
      },
    );
  }
}
