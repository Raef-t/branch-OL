import '/features/exams_to_all_students/presentation/managers/models/batch_subject_model.dart';

class ExamsModel {
  final String? name;
  final String? examContent;
  final String? firstTime;
  final BatchSubjectModel? batchSubjectModel;
  bool isChecked;
  ExamsModel({
    required this.name,
    required this.examContent,
    required this.firstTime,
    required this.batchSubjectModel,
    this.isChecked = false,
  });
  factory ExamsModel.fromJson({required Map<String, dynamic> json}) {
    return ExamsModel(
      name: json['name'] as String?,
      examContent: json['remarks'] as String?,
      firstTime: json['exam_time'] as String?,
      batchSubjectModel: json['batch_subject'] != null
          ? BatchSubjectModel.fromJson(json: json['batch_subject'])
          : null,
    );
  }
}
