import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class PhotoEmployeeService {
  final Dio dio;
  PhotoEmployeeService({required this.dio});
  Future<Response> uploadEmployeePhoto({
    required int employeeId,
    required String filePath,
  }) async {
    FormData formData = FormData.fromMap({
      'photo': await MultipartFile.fromFile(filePath),
    });
    //this way enable me upload photo to backend
    final response = await dio.post(
      '$kEmployeesEndPoint$employeeId/photo',
      data: formData,
    );
    return response;
  }
}
