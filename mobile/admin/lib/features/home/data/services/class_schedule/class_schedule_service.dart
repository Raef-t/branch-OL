import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class ClassScheduleService {
  final Dio dio;
  ClassScheduleService({required this.dio});
  Future<Response> getTodaySchedule({required int instituteBranchId}) async {
    final response = await dio.get(
      kClassScheduleEndPoint,
      queryParameters: {
        'is_default': true,
        'institute_branch_id': instituteBranchId,
      },
    );
    return response;
  }
}
