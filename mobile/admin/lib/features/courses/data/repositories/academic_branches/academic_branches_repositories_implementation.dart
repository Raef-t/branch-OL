import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/change_list_of_dynamic_to_list_of_academic_branches_model_helper.dart';
import '/features/courses/data/repositories/academic_branches/academic_branches_repositories.dart';
import '/features/courses/data/services/academic_branches/academic_branches_service.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';

class AcademicBranchesRepositoriesImplementation
    implements AcademicBranchesRepositories {
  final AcademicBranchesService academicBranchesService;
  AcademicBranchesRepositoriesImplementation({
    required this.academicBranchesService,
  });
  @override
  Future<Either<FailureError, List<AcademicBranchesToCoursesModel>>>
  getAcademicBranches({
    required String genderType,
    required int instituteBranchId,
  }) async {
    try {
      final response = await academicBranchesService.getAcademicBranches(
        genderType: genderType,
        instituteBranchId: instituteBranchId,
      );
      final List<AcademicBranchesToCoursesModel> listOfAcademicBranchesModel =
          changeListOfDynamicToListOfAcademicBranchesModelHelper(
            response: response,
          );
      return Right(listOfAcademicBranchesModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر الفروع الأكاديميه، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
