import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class EmployeesService {
  final Dio dio;
  EmployeesService({required this.dio});
  Future<Response> updateEmployee({
    required int employeeId,
    required String firstName,
    required String lastName,
  }) async {
    final response = await dio.put(
      '$kEmployeesEndPoint$employeeId',
      data: {
        'user_id': employeeId,
        'first_name': firstName,
        'last_name': lastName,
      },
    );
    return response;
  }
}
