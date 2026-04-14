import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class AcademicBranchesCoursesDetailsService {
  final Dio dio;
  AcademicBranchesCoursesDetailsService({required this.dio});
  Future<Response> getAcademicBranches({
    required String genderType,
    required int instituteBranchId,
  }) async {
    final response = await dio.get(
      '$kAcademicBranchEndPoint$genderType',
      queryParameters: {'institute_branch_id': instituteBranchId},
    );
    return response;
  }
}
