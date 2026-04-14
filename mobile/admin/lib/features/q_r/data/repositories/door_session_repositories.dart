import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/q_r/presentation/managers/models/door_session_model.dart';

abstract class DoorSessionRepositories {
  Future<Either<FailureError, DoorSessionModel>> generateDoorSession({
    required String deviceId,
  });
}
