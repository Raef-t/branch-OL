import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class FinancialSummaryService {
  final Dio dio;
  FinancialSummaryService({required this.dio});
  Future<Response> getStudentFinancialSummary({required int studentId}) async {
    final response = await dio.get(
      '$kStudentsEndPoint$studentId/financial-summary',
    );
    return response;
  }
}
