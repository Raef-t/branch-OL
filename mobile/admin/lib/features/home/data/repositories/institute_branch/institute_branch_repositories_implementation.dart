import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/change_list_of_dynamic_to_list_of_institute_branch_model_helper.dart';
import '/features/home/data/repositories/institute_branch/institute_branch_repositories.dart';
import '/features/home/data/services/institute_branch/institute_branch_service.dart';
import '/features/home/presentation/managers/models/institute_branch/institute_branch_model.dart';

class InstituteBranchRepositoriesImplementation
    implements InstituteBranchRepositories {
  final InstituteBranchService instituteBranchService;
  InstituteBranchRepositoriesImplementation({
    required this.instituteBranchService,
  });
  @override
  Future<Either<FailureError, List<InstituteBranchModel>>>
  getInstituteBranches() async {
    try {
      final response = await instituteBranchService.getInstituteBranches();
      List<InstituteBranchModel> listOfInstituteBranchModel =
          changeListOfDynamicToListOfInstituteBranchModelHelper(
            response: response,
          );
      return Right(listOfInstituteBranchModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر فروع المعهد، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
