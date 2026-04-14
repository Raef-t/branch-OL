import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/courses_details/data/repositories/academic_branches_courses_details_repositories.dart';
import '/features/courses_details/data/services/academic_branches_courses_details_service.dart';
import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';

class AcademicBranchesCoursesDetailsRepositoriesImplementation
    implements AcademicBranchesCoursesDetailsRepositories {
  final AcademicBranchesCoursesDetailsService academicBranchesService;
  AcademicBranchesCoursesDetailsRepositoriesImplementation({
    required this.academicBranchesService,
  });
  @override
  Future<Either<FailureError, List<AcademicBranchesCoursesDetailsModel>>>
  getAcademicBranches({
    required String genderType,
    required int instituteBranchId,
  }) async {
    try {
      final response = await academicBranchesService.getAcademicBranches(
        genderType: genderType,
        instituteBranchId: instituteBranchId,
      );
      final List<dynamic> data = response.data['data'];
      final List<AcademicBranchesCoursesDetailsModel>
      listOfAcademicBranchesModel = [];
      for (var academicBranches in data) {
        listOfAcademicBranchesModel.add(
          AcademicBranchesCoursesDetailsModel.fromJson(json: academicBranches),
        );
      }
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
