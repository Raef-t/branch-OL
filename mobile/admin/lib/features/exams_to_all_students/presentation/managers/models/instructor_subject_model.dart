import '/features/exams_to_all_students/presentation/managers/models/subject_model.dart';

class InstructorSubjectModel {
  final SubjectModel? subjectModel;
  InstructorSubjectModel({required this.subjectModel});
  factory InstructorSubjectModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return InstructorSubjectModel(
      subjectModel: json['subject'] != null
          ? SubjectModel.fromJson(json: json['subject'])
          : null,
    );
  }
}
