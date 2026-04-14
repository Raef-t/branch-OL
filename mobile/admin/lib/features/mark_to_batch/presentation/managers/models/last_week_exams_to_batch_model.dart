class LastWeekExamsToBatchModel {
  final String? subjectName;
  final String? date;
  final int? id;
  LastWeekExamsToBatchModel({
    required this.subjectName,
    required this.date,
    required this.id,
  });
  factory LastWeekExamsToBatchModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return LastWeekExamsToBatchModel(
      subjectName: json['exam_name'] as String?,
      date: json['exam_date'] as String?,
      id: json['exam_id'] as int?,
    );
  }
}
