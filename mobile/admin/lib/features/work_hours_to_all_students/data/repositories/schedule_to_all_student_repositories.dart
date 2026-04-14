import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/work_hours_to_all_students/presentation/managers/models/schedule_to_all_student_model.dart';

abstract class ScheduleToAllStudentRepositories {
  Future<Either<FailureError, ScheduleToAllStudentModel>> getSchedule({
    required String type,
    required int id,
    required String? day,
    required int instituteBranchId,
  });
}
