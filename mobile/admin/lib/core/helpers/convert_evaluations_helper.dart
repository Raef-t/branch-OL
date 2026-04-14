import '/features/details_students/presentation/managers/models/monthly_evaluations/monthly_evaluations_model.dart';

List<MonthlyEvaluationModel> convertEvaluationsHelper({
  required Map<String, dynamic> evaluations,
}) {
  List<MonthlyEvaluationModel> list = [];
  evaluations.forEach((key, value) {
    list.add(
      MonthlyEvaluationModel(
        rating: value != null ? (value as num).toDouble() : 0,
      ),
    );
  });
  return list;
}
