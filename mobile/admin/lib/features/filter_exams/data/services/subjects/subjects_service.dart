import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class SubjectsService {
  final Dio dio;
  SubjectsService({required this.dio});
  Future<Response> getSubjectsByAcademicBranch({
    required int academicBranchId,
  }) async {
    final response = await dio.get(
      '$kAcademicBranchEndPoint$academicBranchId/subjects',
    );
    return response;
  }
}
