import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class ExamsService {
  final Dio dio;
  ExamsService({required this.dio});
  Future<Response> getExamsByDate({
    required String date,
    required int branchId,
  }) async {
    final response = await dio.get(
      '$kExamsEndPoint$date',
      data: {'branch_id': branchId},
    );
    return response;
  }
}
