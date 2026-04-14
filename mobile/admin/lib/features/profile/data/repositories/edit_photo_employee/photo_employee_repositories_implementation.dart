import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/profile/data/repositories/edit_photo_employee/photo_employee_repositories.dart';
import '/features/profile/data/services/edit_photo_employee/photo_employee_service.dart';

class PhotoEmployeeRepositoriesImplementation
    implements PhotoEmployeeRepositories {
  final PhotoEmployeeService photoEmployeeService;
  PhotoEmployeeRepositoriesImplementation({required this.photoEmployeeService});
  @override
  Future<Either<FailureError, String>> uploadEmployeePhoto({
    required int employeeId,
    required String filePath,
  }) async {
    try {
      final response = await photoEmployeeService.uploadEmployeePhoto(
        employeeId: employeeId,
        filePath: filePath,
      );
      final photoUrl = response.data['data']['photo_url'] as String;
      return Right(photoUrl);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر تعديل صورة موظف، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
