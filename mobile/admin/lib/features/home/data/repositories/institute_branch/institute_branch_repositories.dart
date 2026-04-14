import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/home/presentation/managers/models/institute_branch/institute_branch_model.dart';

abstract class InstituteBranchRepositories {
  Future<Either<FailureError, List<InstituteBranchModel>>>
  getInstituteBranches();
}
