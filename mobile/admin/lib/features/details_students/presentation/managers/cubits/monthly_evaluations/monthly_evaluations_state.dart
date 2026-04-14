import '/features/details_students/presentation/managers/models/monthly_evaluations/monthly_evaluations_model.dart';

abstract class MonthlyEvaluationState {}

class MonthlyEvaluationInitial extends MonthlyEvaluationState {}

class MonthlyEvaluationLoading extends MonthlyEvaluationState {}

class MonthlyEvaluationSuccess extends MonthlyEvaluationState {
  final List<MonthlyEvaluationModel> evaluations;
  MonthlyEvaluationSuccess({required this.evaluations});
}

class MonthlyEvaluationFailure extends MonthlyEvaluationState {
  final String errorMessage;
  MonthlyEvaluationFailure({required this.errorMessage});
}
