import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/home/presentation/managers/models/class_schedule/class_schedule_model.dart';

abstract class ClassScheduleRepository {
  Future<Either<FailureError, ClassScheduleModel>> getTodaySchedule({
    required int instituteBranchId,
  });
}
