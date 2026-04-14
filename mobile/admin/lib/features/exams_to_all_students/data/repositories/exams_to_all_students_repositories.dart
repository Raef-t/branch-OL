import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';

abstract class ExamsRepositories {
  Future<Either<FailureError, List<ExamsModel>>> getExamsByDate({
    required String date,
    required int branchId,
  });
}
