import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/exams_to_student/presentation/managers/models/current_and_last_model.dart';

abstract class StudentExamsRepository {
  Future<Either<FailureError, CurrentAndLastModel>> getTodayAndWeekExams({
    required int studentId,
  });
}
