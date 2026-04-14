import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class MonthlyEvaluationService {
  final Dio dio;
  MonthlyEvaluationService({required this.dio});
  Future<Response> getMonthlyEvaluations({required int studentId}) async {
    final response = await dio.get(
      '$kStudentsEndPoint$studentId/monthly-evaluation',
    );
    return response;
  }
}
