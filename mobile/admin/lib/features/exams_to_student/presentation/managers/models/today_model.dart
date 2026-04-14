class CurrentModel {
  final String? subjectName;
  final String? date;
  final String? course;
  final String? classRoom;
  CurrentModel({
    required this.subjectName,
    required this.date,
    required this.course,
    required this.classRoom,
  });
  factory CurrentModel.fromJson({required Map<String, dynamic> json}) {
    return CurrentModel(
      subjectName: json['subject_name'] as String?,
      date: json['exam_date'] as String?,
      course: json['batch_name'] as String?,
      classRoom: json['class_section'] as String?,
    );
  }
}
