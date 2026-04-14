import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/courses/presentation/managers/models/total_students/total_students_model.dart';

abstract class TotalStudentsRepositories {
  Future<Either<FailureError, TotalStudentsModel>> getTotalStudents({
    required int branchId,
  });
}
