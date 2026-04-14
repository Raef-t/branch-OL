import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class DetailsStudentsService {
  final Dio dio;
  DetailsStudentsService({required this.dio});
  Future<Response> getDetailsStudentById({required int studentId}) async {
    final response = await dio.get('$kStudentsEndPoint$studentId');
    return response;
  }
}
