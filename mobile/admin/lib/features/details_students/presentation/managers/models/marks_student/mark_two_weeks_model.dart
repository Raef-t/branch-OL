import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';

class MarkTwoWeeksModel {
  final List<ExamResultModel> currentWeekList;
  final List<ExamResultModel> lastWeekList;
  MarkTwoWeeksModel({
    required this.currentWeekList,
    required this.lastWeekList,
  });
}
