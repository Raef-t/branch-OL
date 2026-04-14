import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/mark_to_batch/data/repositories/last_and_current_week_exams_to_batch_repositories.dart';
import '/features/mark_to_batch/data/services/last_and_current_week_exams_to_batch_service.dart';
import '/features/mark_to_batch/presentation/managers/models/current_and_last_weeks_exams_to_batch_model.dart';

class LastAndCurrentWeekExamsToBatchRepositoriesImplementation
    implements LastAndCurrentWeekExamsToBatchRepositories {
  final LastAndCurrentWeekExamsToBatchService
  lastAndCurrentWeekExamsToBatchService;
  LastAndCurrentWeekExamsToBatchRepositoriesImplementation({
    required this.lastAndCurrentWeekExamsToBatchService,
  });
  @override
  Future<Either<FailureError, CurrentAndLastWeeksExamsToBatchModel>>
  getLastAndCurrentTwoWeeksExams({required int batchId}) async {
    try {
      final response = await lastAndCurrentWeekExamsToBatchService
          .getLastTwoWeeksExams(batchId: batchId);
      final data = response.data['data'];
      final currentAndLastWeeksExamsToBatchModel =
          CurrentAndLastWeeksExamsToBatchModel.fromJson(json: data);
      return Right(currentAndLastWeeksExamsToBatchModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر جلب امتحانات شعبه، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
