import '/features/home/presentation/managers/models/class_schedule/supervisor_in_class_schedule_model.dart';

class LessonsModel {
  final String? subjectName;
  final String? course;
  final String? classRoom;
  final String? startTime;
  final String? endTime;
  final String? type;
  final SupervisorInClassScheduleModel? supervisorModel;

  LessonsModel({
    required this.subjectName,
    required this.course,
    required this.classRoom,
    required this.supervisorModel,
    required this.startTime,
    required this.endTime,
    required this.type,
  });

  factory LessonsModel.fromJson({required Map<String, dynamic> json}) {
    return LessonsModel(
      subjectName: json['subject'] as String?,
      course: json['batch_name'] as String?,
      classRoom: json['class_room'] as String?,
      type: json['type'] as String?,
      supervisorModel: json['supervisor'] != null
          ? SupervisorInClassScheduleModel.fromJson(json: json['supervisor'])
          : null,
      startTime: json['start_time'] as String?,
      endTime: json['end_time'] as String?,
    );
  }
}
