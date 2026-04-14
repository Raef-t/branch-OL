import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/courses/data/repositories/total_students/total_students_repositories.dart';
import '/features/courses/data/services/total_students/total_students_service.dart';
import '/features/courses/presentation/managers/models/total_students/total_students_model.dart';

class TotalStudentsRepositoriesImplementation
    implements TotalStudentsRepositories {
  final TotalStudentsService totalStudentsService;
  TotalStudentsRepositoriesImplementation({required this.totalStudentsService});
  @override
  Future<Either<FailureError, TotalStudentsModel>> getTotalStudents({
    required int branchId,
  }) async {
    try {
      final response = await totalStudentsService.getTotalStudents(
        branchId: branchId,
      );
      final dynamic data = response.data['data'];
      final totalStudentsModel = TotalStudentsModel.fromJson(
        json: data is List ? data[0] : data as Map<String, dynamic>,
      );
      return Right(totalStudentsModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر عدد الكلي للطلاب، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
