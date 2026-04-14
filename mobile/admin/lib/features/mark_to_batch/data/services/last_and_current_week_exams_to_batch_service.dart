import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class LastAndCurrentWeekExamsToBatchService {
  final Dio dio;
  LastAndCurrentWeekExamsToBatchService({required this.dio});
  Future<Response> getLastTwoWeeksExams({required int batchId}) async {
    return await dio.get(
      'batches/$batchId/$kExamsLastAndCurrentWeekToBatchEndPoint',
    );
  }
}
