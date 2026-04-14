import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';

abstract class SendMTNMessageRepositories {
  Future<Either<FailureError, bool>> sendSms({
    required String from,
    required List<String> to,
    required String contentMessage,
    required int language,
  });
}
