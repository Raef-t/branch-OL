import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/auth/presentation/managers/models/auth_model.dart';

abstract class AuthRepositories {
  Future<Either<FailureError, AuthModel>> login({
    required String uniqueId,
    required String password,
  });
}
