import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class ScheduleToStudentService {
  final Dio dio;
  ScheduleToStudentService({required this.dio});
  Future<Response> getSchedule({
    required String type,
    required int id,
    required String? day,
    required int instituteBranchId,
  }) async {
    final response = await dio.get(
      kScheduleEndPoint,
      queryParameters: {
        'type': type,
        'id': id,
        'day': day,
        'institute_branch_id': instituteBranchId,
      },
    );
    return response;
  }
}
