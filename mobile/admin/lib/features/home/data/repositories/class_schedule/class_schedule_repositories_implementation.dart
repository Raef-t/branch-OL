import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/change_map_of_string_and_dyanmic_to_list_of_class_schedule_model_helper.dart';
import '/features/home/data/repositories/class_schedule/class_schedule_repositories.dart';
import '/features/home/data/services/class_schedule/class_schedule_service.dart';
import '/features/home/presentation/managers/models/class_schedule/class_schedule_model.dart';

class ClassScheduleRepositoryImplementation implements ClassScheduleRepository {
  final ClassScheduleService classScheduleService;

  ClassScheduleRepositoryImplementation({required this.classScheduleService});

  @override
  Future<Either<FailureError, ClassScheduleModel>> getTodaySchedule({
    required int instituteBranchId,
  }) async {
    try {
      final response = await classScheduleService.getTodaySchedule(
        instituteBranchId: instituteBranchId,
      );
      final ClassScheduleModel classScheduleList =
          changeMapOfStringAndDyanmicToListOfClassScheduleModelHelper(
            response: response,
          );
      return Right(classScheduleList);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر برنامج الدوام، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
