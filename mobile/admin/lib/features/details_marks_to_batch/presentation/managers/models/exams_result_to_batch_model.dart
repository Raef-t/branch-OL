class ExamsResultToBatchModel {
  final String? studentName;
  final String? mark;
  final String? studentPhoto;
  final int? isPassed;
  final DateTime? date;
  ExamsResultToBatchModel({
    required this.studentName,
    required this.mark,
    required this.studentPhoto,
    required this.isPassed,
    required this.date,
  });
  factory ExamsResultToBatchModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return ExamsResultToBatchModel(
      studentName: json['student_name'] as String?,
      mark: json['obtained_marks'] as String?,
      studentPhoto: json['student_photo'] as String?,
      isPassed: json['is_passed'] as int?,
      date: json['created_at'] != null
          ? DateTime.parse(json['created_at']).toLocal()
          : null,
    );
  }
}
