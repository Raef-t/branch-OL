import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class TotalStudentsService {
  final Dio dio;
  TotalStudentsService({required this.dio});
  Future<Response> getTotalStudents({required int branchId}) async {
    final response = await dio.get(
      kTotalStudentsEndPoint,
      queryParameters: {'branch_id': branchId},
    );
    return response;
  }
}
