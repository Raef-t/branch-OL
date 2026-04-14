import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/filter_exams/presentation/managers/models/subjects/subjects_model.dart';

abstract class SubjectsRepositories {
  Future<Either<FailureError, List<SubjectsModel>>>
  getSubjectsByAcademicBranch({required int academicBranchId});
}
