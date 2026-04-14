import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/details_students/data/repositories/details_students/details_students_repositories.dart';
import '/features/details_students/data/services/details_students/details_students_service.dart';
import '/features/details_students/presentation/managers/models/details_students/details_students_model.dart';

class DetailsStudentsRepositoriesImplementation
    implements DetailsStudentsRepositories {
  final DetailsStudentsService detailsStudentsService;
  DetailsStudentsRepositoriesImplementation({
    required this.detailsStudentsService,
  });
  @override
  Future<Either<FailureError, DetailsStudentsModel>> getDetailsStudentById({
    required int studentId,
  }) async {
    try {
      final response = await detailsStudentsService.getDetailsStudentById(
        studentId: studentId,
      );
      final detailsStudentsModel = DetailsStudentsModel.fromJson(
        json: response.data['data'],
      );
      return Right(detailsStudentsModel);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر تفاصيل طالب، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
