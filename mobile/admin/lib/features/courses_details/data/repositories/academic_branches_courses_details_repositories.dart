import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';

abstract class AcademicBranchesCoursesDetailsRepositories {
  Future<Either<FailureError, List<AcademicBranchesCoursesDetailsModel>>>
  getAcademicBranches({
    required String genderType,
    required int instituteBranchId,
  });
}
