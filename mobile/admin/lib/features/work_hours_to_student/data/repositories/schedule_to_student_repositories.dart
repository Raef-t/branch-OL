import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/work_hours_to_student/presentation/managers/models/schedule_to_student_model.dart';

abstract class ScheduleToStudentRepositories {
  Future<Either<FailureError, ScheduleToStudentModel>> getSchedule({
    required String type,
    required int id,
    required String? day,
    required int instituteBranchId,
  });
}
