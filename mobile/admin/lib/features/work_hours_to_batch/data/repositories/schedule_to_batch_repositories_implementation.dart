import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/work_hours_to_batch/data/repositories/schedule_to_batch_repositories.dart';
import '/features/work_hours_to_batch/data/services/schedule_to_batch_service.dart';
import '/features/work_hours_to_batch/presentation/managers/models/schedule_to_batch_model.dart';

class ScheduleToBatchRepositoriesImplementation
    implements ScheduleToBatchRepositories {
  final ScheduleToBatchService scheduleToBatchService;
  ScheduleToBatchRepositoriesImplementation({
    required this.scheduleToBatchService,
  });
  @override
  Future<Either<FailureError, ScheduleToBatchModel>> getSchedule({
    required String type,
    required int id,
    required String? day,
    required int instituteBranchId,
  }) async {
    try {
      final response = await scheduleToBatchService.getSchedule(
        type: type,
        id: id,
        day: day,
        instituteBranchId: instituteBranchId,
      );
      final scheduleToBatchModel = ScheduleToBatchModel.fromJson(
        json: response.data['data'],
      );
      return Right(scheduleToBatchModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر برنامج دوام لشعبه، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
