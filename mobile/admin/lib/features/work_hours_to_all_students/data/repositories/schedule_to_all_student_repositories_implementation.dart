import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/work_hours_to_all_students/data/repositories/schedule_to_all_student_repositories.dart';
import '/features/work_hours_to_all_students/data/services/schedule_to_all_student_service.dart';
import '/features/work_hours_to_all_students/presentation/managers/models/schedule_to_all_student_model.dart';

class ScheduleToAllStudentRepositoriesImplementation
    implements ScheduleToAllStudentRepositories {
  final ScheduleToAllStudentService scheduleToBatchService;
  ScheduleToAllStudentRepositoriesImplementation({
    required this.scheduleToBatchService,
  });
  @override
  Future<Either<FailureError, ScheduleToAllStudentModel>> getSchedule({
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
      final scheduleToBatchModel = ScheduleToAllStudentModel.fromJson(
        json: response.data['data'],
      );
      return Right(scheduleToBatchModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر برنامج دوام معهد، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
