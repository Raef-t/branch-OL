import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/work_hours_to_batch/presentation/managers/models/schedule_to_batch_model.dart';

abstract class ScheduleToBatchRepositories {
  Future<Either<FailureError, ScheduleToBatchModel>> getSchedule({
    required String type,
    required int id,
    required String? day,
    required int instituteBranchId,
  });
}
