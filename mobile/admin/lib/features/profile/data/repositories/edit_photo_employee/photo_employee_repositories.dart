import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';

abstract class PhotoEmployeeRepositories {
  Future<Either<FailureError, String>> uploadEmployeePhoto({
    required int employeeId,
    required String filePath,
  });
}
