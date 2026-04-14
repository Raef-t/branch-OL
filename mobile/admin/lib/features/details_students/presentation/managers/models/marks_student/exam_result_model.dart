class ExamResultModel {
  final String? mark;
  final String? subjectName;
  final int? isPassed;
  final DateTime? date;
  ExamResultModel({
    required this.mark,
    required this.isPassed,
    required this.subjectName,
    required this.date,
  });
  factory ExamResultModel.fromJson({required Map<String, dynamic> json}) {
    return ExamResultModel(
      mark: json['obtained_marks'] as String?,
      isPassed: json['is_passed'] as int?,
      subjectName: json['subject_name'] as String?,
      date: json['created_at'] != null
          ? DateTime.parse(json['created_at']).toLocal()
          //this line will change the time in backend(2025-12-29T16:33:47.000000Z) to DateTime Dart know it and i can deal with it
          : null,
    );
  }
}
