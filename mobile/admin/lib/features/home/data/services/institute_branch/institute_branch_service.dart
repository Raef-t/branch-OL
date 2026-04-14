import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class InstituteBranchService {
  final Dio dio;
  InstituteBranchService({required this.dio});
  Future<Response> getInstituteBranches() async {
    final response = await dio.get(kInstituteBrnachEndPoint);
    return response;
  }
}
