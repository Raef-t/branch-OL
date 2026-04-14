import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';

abstract class AttendanceRepositories {
  Future<Either<FailureError, List<AttendanceModel>>> getAttendanceLog({
    required int studentId,
    required String range,
  });
}
