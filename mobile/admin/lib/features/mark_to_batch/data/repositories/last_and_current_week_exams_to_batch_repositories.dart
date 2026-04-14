import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/mark_to_batch/presentation/managers/models/current_and_last_weeks_exams_to_batch_model.dart';

abstract class LastAndCurrentWeekExamsToBatchRepositories {
  Future<Either<FailureError, CurrentAndLastWeeksExamsToBatchModel>>
  getLastAndCurrentTwoWeeksExams({required int batchId});
}
