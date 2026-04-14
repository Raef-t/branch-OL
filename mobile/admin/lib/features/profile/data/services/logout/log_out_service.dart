import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class LogOutService {
  final Dio dio;
  LogOutService({required this.dio});
  Future<Response> logout() async {
    final response = await dio.post(kLogOutEndPoint);
    return response;
  }
}
