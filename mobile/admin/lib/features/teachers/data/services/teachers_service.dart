import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class TeachersService {
  final Dio dio;
  TeachersService({required this.dio});
  Future<Response> getAllTeachers() async {
    final response = await dio.get(kTeachersEndPoint);
    return response;
  }
}
