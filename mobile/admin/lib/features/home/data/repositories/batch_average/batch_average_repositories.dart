import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/home/presentation/managers/models/batch_average/batch_average_model.dart';

abstract class BatchAverageRepositories {
  Future<Either<FailureError, List<BatchAverageModel>>> getBatchAverages({
    required int instituteBranchId,
    required int academicBranchId,
  });
}
