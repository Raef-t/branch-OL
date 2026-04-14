import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class BatchStudentsService {
  final Dio dio;
  BatchStudentsService({required this.dio});
  Future<Response> getStudentsByBatchId({required int batchId}) async {
    final response = await dio.get('$kBatchStudentsEndPoint$batchId/students');
    return response;
  }
}
