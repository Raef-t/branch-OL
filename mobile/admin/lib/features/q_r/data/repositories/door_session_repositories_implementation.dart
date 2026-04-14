import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/q_r/data/repositories/door_session_repositories.dart';
import '/features/q_r/data/services/door_session_service.dart';
import '/features/q_r/presentation/managers/models/door_session_model.dart';

class DoorSessionRepositoriesImplementation implements DoorSessionRepositories {
  final DoorSessionService doorSessionService;
  DoorSessionRepositoriesImplementation({required this.doorSessionService});
  @override
  Future<Either<FailureError, DoorSessionModel>> generateDoorSession({
    required String deviceId,
  }) async {
    try {
      final response = await doorSessionService.generateDoorSession(
        deviceId: deviceId,
      );
      final model = DoorSessionModel.fromJson(json: response.data['data']);
      return Right(model);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر توليد كيو ار، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
