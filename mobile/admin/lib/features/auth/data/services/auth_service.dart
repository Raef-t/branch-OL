import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class AuthService {
  final Dio dio;
  AuthService({required this.dio});
  Future<Response> login({
    required String uniqueId,
    required String password,
  }) async {
    final response = await dio.post(
      kAuthEndPoint,
      data: {'unique_id': uniqueId, 'password': password},
    );
    return response;
  }
}
