class CurrentWeekExamsToBatchModel {
  final String? subjectName;
  final String? date;
  final int? id;
  CurrentWeekExamsToBatchModel({
    required this.subjectName,
    required this.date,
    required this.id,
  });
  factory CurrentWeekExamsToBatchModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return CurrentWeekExamsToBatchModel(
      subjectName: json['exam_name'] as String?,
      date: json['exam_date'] as String?,
      id: json['exam_id'] as int?,
    );
  }
}
