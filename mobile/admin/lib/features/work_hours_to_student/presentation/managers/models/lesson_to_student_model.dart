class LessonToStudentModel {
  final String? course;
  final String? subjectName;
  final String? classRoom;
  final String? startTime;
  final String? endTime;
  final String? type;
  LessonToStudentModel({
    required this.course,
    required this.subjectName,
    required this.classRoom,
    required this.startTime,
    required this.endTime,
    required this.type,
  });
  factory LessonToStudentModel.fromJson({required Map<String, dynamic> json}) {
    return LessonToStudentModel(
      course: json['batch_name'] as String?,
      subjectName: json['subject'] as String?,
      classRoom: json['class_room'] as String?,
      startTime: json['start_time'] as String?,
      endTime: json['end_time'] as String?,
      type: json['type'] as String?,
    );
  }
}
