import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/convert_evaluations_helper.dart';
import '/features/details_students/data/repositories/monthly_evaluations/monthly_evaluations_repositories.dart';
import '/features/details_students/data/services/monthly_evaluations/monthly_evaluations_service.dart';
import '/features/details_students/presentation/managers/models/monthly_evaluations/monthly_evaluations_model.dart';

class MonthlyEvaluationRepositoryImplementation
    implements MonthlyEvaluationRepository {
  final MonthlyEvaluationService service;
  MonthlyEvaluationRepositoryImplementation({required this.service});
  @override
  Future<Either<FailureError, List<MonthlyEvaluationModel>>>
  getMonthlyEvaluations({required int studentId}) async {
    try {
      final response = await service.getMonthlyEvaluations(
        studentId: studentId,
      );
      final evaluations =
          response.data['data']['evaluations'] as Map<String, dynamic>;
      final list = convertEvaluationsHelper(evaluations: evaluations);
      return Right(list);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return left(ErrorServer(errorMessageInFailureError: e.toString()));
    }
  }
}
