import '/features/exams_to_all_students/presentation/managers/models/batch_model.dart';
import '/features/exams_to_all_students/presentation/managers/models/class_room_model.dart';
import '/features/exams_to_all_students/presentation/managers/models/instructor_subject_model.dart';

class BatchSubjectModel {
  final BatchModel? batchModel;
  final ClassRoomModel? classRoomModel;
  final InstructorSubjectModel? instructorSubjectModel;
  BatchSubjectModel({
    required this.batchModel,
    required this.classRoomModel,
    required this.instructorSubjectModel,
  });
  factory BatchSubjectModel.fromJson({required Map<String, dynamic> json}) {
    return BatchSubjectModel(
      batchModel: json['batch'] != null
          ? BatchModel.fromJson(json: json['batch'])
          : null,
      classRoomModel: json['class_room'] != null
          ? ClassRoomModel.fromJson(json: json['class_room'])
          : null,
      instructorSubjectModel: json['instructor_subject'] != null
          ? InstructorSubjectModel.fromJson(json: json['instructor_subject'])
          : null,
    );
  }
}
