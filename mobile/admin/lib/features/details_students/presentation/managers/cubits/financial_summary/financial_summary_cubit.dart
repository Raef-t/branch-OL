import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/details_students/data/repositories/financial_summary/financial_summary_repositories_implementation.dart';
import '/features/details_students/presentation/managers/cubits/financial_summary/financial_summary_state.dart';

class FinancialSummaryCubit extends Cubit<FinancialSummaryState> {
  FinancialSummaryCubit({
    required this.financialSummaryRepositoriesImplementation,
  }) : super(FinancialSummaryInitialState());
  final FinancialSummaryRepositoriesImplementation
  financialSummaryRepositoriesImplementation;
  Future<void> getStudentFinancialSummary() async {
    emit(FinancialSummaryLoadingState());
    final studentId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyStudentIdInSharedPreferences,
    );
    final result = await financialSummaryRepositoriesImplementation
        .getStudentFinancialSummary(studentId: studentId ?? 0);
    result.fold(
      (failure) => emit(
        FinancialSummaryFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (financialSummary) => emit(
        FinancialSummarySuccessState(
          financialSummaryModelInCubit: financialSummary,
        ),
      ),
    );
  }
}
