import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/details_students/presentation/managers/models/monthly_evaluations/monthly_evaluations_model.dart';

abstract class MonthlyEvaluationRepository {
  Future<Either<FailureError, List<MonthlyEvaluationModel>>>
  getMonthlyEvaluations({required int studentId});
}
