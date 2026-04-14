import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class BatchAverageService {
  final Dio dio;
  BatchAverageService({required this.dio});
  Future<Response> getBatchAverages({
    required int instituteBranchId,
    required int academicBranchId,
  }) async {
    final response = await dio.get(
      kBatchesAverageEndPoint,
      queryParameters: {
        'institute_branch_id': instituteBranchId,
        'academic_branch_id': academicBranchId,
      },
    );
    return response;
  }
}
