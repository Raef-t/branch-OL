import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/details_students/data/repositories/mark_students/mark_students_repositories.dart';
import '/features/details_students/data/services/mark_students/mark_students_service.dart';
import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';
import '/features/details_students/presentation/managers/models/marks_student/mark_two_weeks_model.dart';

class MarkStudentsRepositoriesImplementation
    implements MarkStudentsRepositories {
  final MarkStudentsService markStudentsService;
  MarkStudentsRepositoriesImplementation({required this.markStudentsService});
  @override
  Future<Either<FailureError, MarkTwoWeeksModel>> getLastTwoWeeksExams({
    required int studentId,
  }) async {
    try {
      final response = await markStudentsService.getLastTwoWeeksExams(
        studentId: studentId,
      );
      final data = response.data['data'];
      final currentWeekList = (data['current_week'] as List)
          .map((e) => ExamResultModel.fromJson(json: e))
          .toList();
      final lastWeekList = (data['last_week'] as List)
          .map((e) => ExamResultModel.fromJson(json: e))
          .toList();
      return Right(
        MarkTwoWeeksModel(
          currentWeekList: currentWeekList,
          lastWeekList: lastWeekList,
        ),
      );
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر علامات الطالب، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
