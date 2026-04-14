import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class AttendanceService {
  final Dio dio;
  AttendanceService({required this.dio});
  Future<Response> getAttendanceLog({
    required int studentId,
    required String range,
  }) async {
    final response = await dio.get(
      'students/$studentId/$kAttendanceStudentEndPoint',
      queryParameters: {'range': range},
    );
    return response;
  }
}
