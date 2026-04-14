import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/details_students/presentation/managers/models/financial_summary/financial_summary_model.dart';

abstract class FinancialSummaryRepositories {
  Future<Either<FailureError, FinancialSummaryModel>>
  getStudentFinancialSummary({required int studentId});
}
