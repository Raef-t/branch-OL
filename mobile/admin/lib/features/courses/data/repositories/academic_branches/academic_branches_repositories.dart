import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';

abstract class AcademicBranchesRepositories {
  Future<Either<FailureError, List<AcademicBranchesToCoursesModel>>>
  getAcademicBranches({
    required String genderType,
    required int instituteBranchId,
  });
}
