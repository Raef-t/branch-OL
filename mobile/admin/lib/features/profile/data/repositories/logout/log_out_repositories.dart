import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';

abstract class LogOutRepositories {
  Future<Either<FailureError, String>> logout();
}
