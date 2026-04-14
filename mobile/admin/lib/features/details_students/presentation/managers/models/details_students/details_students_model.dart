import '/features/details_students/presentation/managers/models/details_students/details_students_batch_model.dart';

class DetailsStudentsModel {
  final String? studentName;
  final String? studentPhoto;
  final DetailsStudentsBatchModel? detailsStudentsBatchModel;
  final bool? studentAttendance;
  DetailsStudentsModel({
    required this.studentName,
    required this.studentPhoto,
    required this.detailsStudentsBatchModel,
    required this.studentAttendance,
  });
  factory DetailsStudentsModel.fromJson({required Map<String, dynamic> json}) {
    return DetailsStudentsModel(
      studentName: json['full_name'] as String?,
      studentPhoto: json['profile_photo_url'] as String?,
      detailsStudentsBatchModel: json['batch'] != null
          ? DetailsStudentsBatchModel.fromJson(json: json['batch'])
          : null,
      studentAttendance: json['attended_today'] as bool?,
    );
  }
}
