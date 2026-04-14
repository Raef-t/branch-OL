import '/features/work_hours_to_all_students/presentation/managers/models/lesson_to_all_student_model.dart';

class PeriodsToAllStudentModel {
  final String periodName; // مثال: الحصة 1
  final List<LessonToAllStudentModel> listOfLessonModel;
  PeriodsToAllStudentModel({
    required this.periodName,
    required this.listOfLessonModel,
  });
  factory PeriodsToAllStudentModel.fromJson({
    required String periodName,
    required List<dynamic> json,
  }) {
    return PeriodsToAllStudentModel(
      periodName: periodName,
      listOfLessonModel: json
          .map((e) => LessonToAllStudentModel.fromJson(json: e))
          .toList(),
    );
  }
}
