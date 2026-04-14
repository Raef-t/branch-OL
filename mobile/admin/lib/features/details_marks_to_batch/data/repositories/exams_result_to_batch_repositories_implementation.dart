import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/details_marks_to_batch/data/repositories/exams_result_to_batch_repositories.dart';
import '/features/details_marks_to_batch/data/services/exams_result_to_batch_service.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';

class ExamsResultToBatchRepositoriesImplementation
    implements ExamsResultToBatchRepositories {
  final ExamsResultToBatchService examsResultToBatchService;
  ExamsResultToBatchRepositoriesImplementation({
    required this.examsResultToBatchService,
  });
  @override
  Future<Either<FailureError, List<ExamsResultToBatchModel>>> getExamsResults({
    required int subjectId,
  }) async {
    try {
      final response = await examsResultToBatchService.getExamsResults(
        subjectId: subjectId,
      );
      final List<dynamic> items = response.data['data']['items'];
      final listOfExamsResultToBatchModel = items
          .map((json) => ExamsResultToBatchModel.fromJson(json: json))
          .toList();
      return Right(listOfExamsResultToBatchModel);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر جلب علامات شعبه، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
