import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/filter_exams/data/repositories/subjects/subjects_repositories.dart';
import '/features/filter_exams/data/services/subjects/subjects_service.dart';
import '/features/filter_exams/presentation/managers/models/subjects/subjects_model.dart';

class SubjectsRepositoriesImplementation implements SubjectsRepositories {
  final SubjectsService subjectsService;
  SubjectsRepositoriesImplementation({required this.subjectsService});
  @override
  Future<Either<FailureError, List<SubjectsModel>>>
  getSubjectsByAcademicBranch({required int academicBranchId}) async {
    try {
      final response = await subjectsService.getSubjectsByAcademicBranch(
        academicBranchId: academicBranchId,
      );
      final List<dynamic> listOfData = response.data['data'];
      final List<SubjectsModel> listOfSubjectsModel = listOfData
          .map((e) => SubjectsModel.fromJson(json: e))
          .toList();
      return Right(listOfSubjectsModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر جلب مواد فرع معين، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
