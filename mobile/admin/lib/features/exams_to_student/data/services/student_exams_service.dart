import 'package:dio/dio.dart';

class StudentExamsService {
  final Dio dio;

  StudentExamsService({required this.dio});

  Future<Response> getTodayAndWeekExams({required int studentId}) async {
    return await dio.get('students/$studentId/exams/today-and-week');
  }
}
