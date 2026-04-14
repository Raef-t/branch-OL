import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/change_list_of_response_to_list_of_exams_model_helper.dart';
import '/features/exams_to_all_students/data/repositories/exams_to_all_students_repositories.dart';
import '/features/exams_to_all_students/data/services/exams_to_all_students_service.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';

class ExamsRepositoriesImplementation implements ExamsRepositories {
  final ExamsService examsService;
  ExamsRepositoriesImplementation({required this.examsService});
  @override
  Future<Either<FailureError, List<ExamsModel>>> getExamsByDate({
    required String date,
    required int branchId,
  }) async {
    try {
      final response = await examsService.getExamsByDate(
        date: date,
        branchId: branchId,
      );
      List<ExamsModel> examsList = changeListOfResponseToListOfExamsModelHelper(
        response: response,
      );
      return Right(examsList);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر المذاكرات، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
