import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/exams_to_student/data/repositories/student_exams_repositories.dart';
import '/features/exams_to_student/data/services/student_exams_service.dart';
import '/features/exams_to_student/presentation/managers/models/current_and_last_model.dart';

class StudentExamsRepositoryImplementation implements StudentExamsRepository {
  final StudentExamsService studentExamsService;

  StudentExamsRepositoryImplementation({required this.studentExamsService});

  @override
  Future<Either<FailureError, CurrentAndLastModel>> getTodayAndWeekExams({
    required int studentId,
  }) async {
    try {
      final response = await studentExamsService.getTodayAndWeekExams(
        studentId: studentId,
      );

      final model = CurrentAndLastModel.fromJson(json: response.data['data']);

      return Right(model);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ غير معروف أثناء جلب امتحانات الطالب: ${e.toString()}',
        ),
      );
    }
  }
}
