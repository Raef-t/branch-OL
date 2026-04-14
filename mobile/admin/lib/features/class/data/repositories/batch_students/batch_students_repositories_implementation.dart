import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/change_list_of_dynamic_to_list_of_batch_students_model_helper.dart';
import '/features/class/data/repositories/batch_students/batch_students_repositories.dart';
import '/features/class/data/services/batch_students/batch_students_service.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';

class BatchStudentsRepositoriesImplementation
    implements BatchStudentsRepositories {
  final BatchStudentsService batchStudentsService;
  BatchStudentsRepositoriesImplementation({required this.batchStudentsService});
  @override
  Future<Either<FailureError, List<BatchStudentsModel>>> getStudentsByBatchId({
    required int batchId,
  }) async {
    try {
      final response = await batchStudentsService.getStudentsByBatchId(
        batchId: batchId,
      );
      final listOfBatchStudentsModel =
          changeListOfDynamicToListOfBatchStudentsModelHelper(
            response: response,
          );
      return Right(listOfBatchStudentsModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر طلاب دفعة ما، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
