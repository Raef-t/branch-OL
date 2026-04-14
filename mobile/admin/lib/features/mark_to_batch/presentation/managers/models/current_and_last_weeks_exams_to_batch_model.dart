import '/features/mark_to_batch/presentation/managers/models/current_week_exams_to_batch_model.dart';
import '/features/mark_to_batch/presentation/managers/models/last_week_exams_to_batch_model.dart';

class CurrentAndLastWeeksExamsToBatchModel {
  final List<CurrentWeekExamsToBatchModel>? listOfCurrentWeekMarks;
  final List<LastWeekExamsToBatchModel>? listOfLastWeekMarks;
  CurrentAndLastWeeksExamsToBatchModel({
    required this.listOfCurrentWeekMarks,
    required this.listOfLastWeekMarks,
  });
  factory CurrentAndLastWeeksExamsToBatchModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return CurrentAndLastWeeksExamsToBatchModel(
      listOfCurrentWeekMarks: json['current_week'] != null
          ? (json['current_week'] as List<dynamic>)
                .map((e) => CurrentWeekExamsToBatchModel.fromJson(json: e))
                .toList()
          : null,
      listOfLastWeekMarks: json['last_week'] != null
          ? (json['last_week'] as List<dynamic>)
                .map((e) => LastWeekExamsToBatchModel.fromJson(json: e))
                .toList()
          : null,
    );
  }
}
