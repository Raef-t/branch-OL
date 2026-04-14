import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/change_list_of_response_to_list_of_teachers_model_helper.dart';
import '/features/teachers/data/repositories/teachers_repositories.dart';
import '/features/teachers/data/services/teachers_service.dart';
import '/features/teachers/presentation/managers/models/teachers_model.dart';

class TeachersRepositoriesImplementation implements TeachersRepositories {
  final TeachersService teachersService;
  TeachersRepositoriesImplementation({required this.teachersService});
  @override
  Future<Either<FailureError, List<TeachersModel>>> getAllTeachers() async {
    try {
      final response = await teachersService.getAllTeachers();
      final teachersList = changeListOfResponseToListOfTeachersModelHelper(
        response: response,
      );
      return Right(teachersList);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر اساتذة، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
