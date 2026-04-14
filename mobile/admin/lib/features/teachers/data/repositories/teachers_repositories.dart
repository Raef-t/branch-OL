import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/teachers/presentation/managers/models/teachers_model.dart';

abstract class TeachersRepositories {
  Future<Either<FailureError, List<TeachersModel>>> getAllTeachers();
}
