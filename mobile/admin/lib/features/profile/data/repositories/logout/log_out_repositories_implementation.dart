import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/profile/data/repositories/logout/log_out_repositories.dart';
import '/features/profile/data/services/logout/log_out_service.dart';

class LogOutRepositoriesImplementation implements LogOutRepositories {
  final LogOutService logOutService;
  LogOutRepositoriesImplementation({required this.logOutService});
  @override
  Future<Either<FailureError, String>> logout() async {
    try {
      final response = await logOutService.logout();
      final message = response.data['message'] as String? ?? 'تم تسجيل الخروج';
      return Right(message);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر تسجيل خروج، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
