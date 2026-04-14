import '/features/work_hours_to_student/presentation/managers/models/lesson_to_student_model.dart';

class PeriodsToStudentModel {
  final String periodName; // مثال: الحصة 1
  final List<LessonToStudentModel> listOfLessonModel;
  PeriodsToStudentModel({
    required this.periodName,
    required this.listOfLessonModel,
  });
  factory PeriodsToStudentModel.fromJson({
    required String periodName,
    required List<dynamic> json,
  }) {
    return PeriodsToStudentModel(
      periodName: periodName,
      listOfLessonModel: json
          .map((e) => LessonToStudentModel.fromJson(json: e))
          .toList(),
    );
  }
}
