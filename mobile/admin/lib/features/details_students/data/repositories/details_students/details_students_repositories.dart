import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/details_students/presentation/managers/models/details_students/details_students_model.dart';

abstract class DetailsStudentsRepositories {
  Future<Either<FailureError, DetailsStudentsModel>> getDetailsStudentById({
    required int studentId,
  });
}
