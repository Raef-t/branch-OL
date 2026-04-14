import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';

abstract class ExamsResultToBatchRepositories {
  Future<Either<FailureError, List<ExamsResultToBatchModel>>> getExamsResults({
    required int subjectId,
  });
}
