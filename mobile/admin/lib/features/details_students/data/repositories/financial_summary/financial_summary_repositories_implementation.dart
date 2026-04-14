import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/details_students/data/repositories/financial_summary/financial_summary_repositories.dart';
import '/features/details_students/data/services/financial_summary/financial_summary_service.dart';
import '/features/details_students/presentation/managers/models/financial_summary/financial_summary_model.dart';

class FinancialSummaryRepositoriesImplementation
    implements FinancialSummaryRepositories {
  final FinancialSummaryService financialSummaryService;
  FinancialSummaryRepositoriesImplementation({
    required this.financialSummaryService,
  });
  @override
  Future<Either<FailureError, FinancialSummaryModel>>
  getStudentFinancialSummary({required int studentId}) async {
    try {
      final response = await financialSummaryService.getStudentFinancialSummary(
        studentId: studentId,
      );
      final financialSummaryModel = FinancialSummaryModel.fromJson(
        json: response.data['data'],
      );
      return Right(financialSummaryModel);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر دفعات، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
