import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/attendance/data/repositories/attendance_repositories.dart';
import '/features/attendance/data/services/attendance_service.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';

class AttendanceRepositoriesImplementation implements AttendanceRepositories {
  final AttendanceService attendanceService;
  AttendanceRepositoriesImplementation({required this.attendanceService});
  @override
  Future<Either<FailureError, List<AttendanceModel>>> getAttendanceLog({
    required int studentId,
    required String range,
  }) async {
    try {
      final response = await attendanceService.getAttendanceLog(
        studentId: studentId,
        range: range,
      );
      final List<dynamic> listOfData = response.data['records'] as List;
      final List<AttendanceModel> listOfAttendance = listOfData
          .map((e) => AttendanceModel.fromJson(json: e))
          .toList();
      return Right(listOfAttendance);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر غياب و حضور طالب، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
