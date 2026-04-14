import '/features/details_students/presentation/managers/models/financial_summary/financial_summary_model.dart';

abstract class FinancialSummaryState {}

class FinancialSummaryInitialState extends FinancialSummaryState {}

class FinancialSummaryLoadingState extends FinancialSummaryState {}

class FinancialSummarySuccessState extends FinancialSummaryState {
  final FinancialSummaryModel financialSummaryModelInCubit;
  FinancialSummarySuccessState({required this.financialSummaryModelInCubit});
}

class FinancialSummaryFailureState extends FinancialSummaryState {
  final String errorMessageInCubit;
  FinancialSummaryFailureState({required this.errorMessageInCubit});
}
