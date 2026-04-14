class LastModel {
  final String? subjectName;
  final String? date;
  final String? course;
  final String? classRoom;
  LastModel({
    required this.subjectName,
    required this.date,
    required this.course,
    required this.classRoom,
  });
  factory LastModel.fromJson({required Map<String, dynamic> json}) {
    return LastModel(
      subjectName: json['subject_name'] as String?,
      date: json['exam_date'] as String?,
      course: json['batch_name'] as String?,
      classRoom: json['class_section'] as String?,
    );
  }
}
