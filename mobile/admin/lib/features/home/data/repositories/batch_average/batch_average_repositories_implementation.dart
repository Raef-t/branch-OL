import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/change_list_of_dynamic_to_list_of_batch_average_model_helper.dart';
import '/features/home/data/repositories/batch_average/batch_average_repositories.dart';
import '/features/home/data/services/batch_average/batch_average_service.dart';
import '/features/home/presentation/managers/models/batch_average/batch_average_model.dart';

class BatchAverageRepositoriesImplementation
    implements BatchAverageRepositories {
  final BatchAverageService batchAverageService;
  BatchAverageRepositoriesImplementation({required this.batchAverageService});
  @override
  Future<Either<FailureError, List<BatchAverageModel>>> getBatchAverages({
    required int instituteBranchId,
    required int academicBranchId,
  }) async {
    try {
      final response = await batchAverageService.getBatchAverages(
        instituteBranchId: instituteBranchId,
        academicBranchId: academicBranchId,
      );
      List<BatchAverageModel> listOfBatchAverageModel =
          changeListOfDynamicToListOfBatchAverageModelHelper(
            response: response,
          );
      return Right(listOfBatchAverageModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر دورات المتفوقة، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
