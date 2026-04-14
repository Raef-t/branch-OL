import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';

abstract class BatchStudentsRepositories {
  Future<Either<FailureError, List<BatchStudentsModel>>> getStudentsByBatchId({
    required int batchId,
  });
}
