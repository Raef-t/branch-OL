import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class MarkStudentsService {
  final Dio dio;
  MarkStudentsService({required this.dio});
  Future<Response> getLastTwoWeeksExams({required int studentId}) async {
    final response = await dio.get(
      '$kStudentsEndPoint$studentId/exam-results/last-two-weeks',
    );
    return response;
  }
}
