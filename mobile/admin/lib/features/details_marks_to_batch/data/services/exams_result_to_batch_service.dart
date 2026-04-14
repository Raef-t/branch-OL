import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class ExamsResultToBatchService {
  final Dio dio;
  ExamsResultToBatchService({required this.dio});
  Future<Response> getExamsResults({required int subjectId}) async {
    final response = await dio.get('$kExamsResultToBatchEndPoint$subjectId');
    return response;
  }
}
