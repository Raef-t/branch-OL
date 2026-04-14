import '/features/work_hours_to_batch/presentation/managers/models/lesson_to_batch_model.dart';

class PeriodsToBatchModel {
  final String periodName; // مثال: الحصة 1
  final List<LessonToBatchModel> listOfLessonModel;
  PeriodsToBatchModel({
    required this.periodName,
    required this.listOfLessonModel,
  });
  factory PeriodsToBatchModel.fromJson({
    required String periodName,
    required List<dynamic> json,
  }) {
    return PeriodsToBatchModel(
      periodName: periodName,
      listOfLessonModel: json
          .map((e) => LessonToBatchModel.fromJson(json: e))
          .toList(),
    );
  }
}
