import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/work_hours_to_student/data/repositories/schedule_to_student_repositories.dart';
import '/features/work_hours_to_student/data/services/schedule_to_student_service.dart';
import '/features/work_hours_to_student/presentation/managers/models/schedule_to_student_model.dart';

class ScheduleToStudentRepositoriesImplementation
    implements ScheduleToStudentRepositories {
  final ScheduleToStudentService scheduleToStudentService;
  ScheduleToStudentRepositoriesImplementation({
    required this.scheduleToStudentService,
  });
  @override
  Future<Either<FailureError, ScheduleToStudentModel>> getSchedule({
    required String type,
    required int id,
    required String? day,
    required int instituteBranchId,
  }) async {
    try {
      final response = await scheduleToStudentService.getSchedule(
        type: type,
        id: id,
        day: day,
        instituteBranchId: instituteBranchId,
      );
      final scheduleToStudentModel = ScheduleToStudentModel.fromJson(
        json: response.data['data'],
      );
      return Right(scheduleToStudentModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر برنامج دوام لطالب، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
