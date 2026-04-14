import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/details_students/presentation/managers/models/marks_student/mark_two_weeks_model.dart';

abstract class MarkStudentsRepositories {
  Future<Either<FailureError, MarkTwoWeeksModel>> getLastTwoWeeksExams({
    required int studentId,
  });
}
