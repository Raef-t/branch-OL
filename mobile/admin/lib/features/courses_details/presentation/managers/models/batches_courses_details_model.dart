import '/features/courses_details/presentation/managers/models/supervisor_courses_details_model.dart';

class BatchesCoursesDetailsModel {
  final String? batchName;
  final String? startDate;
  final int? isClassroomFull;
  final int? attendancePercentage;
  final int? id;
  final SupervisorCoursesDetailsModel? supervisorInAcademicBranchModel;

  BatchesCoursesDetailsModel({
    required this.batchName,
    required this.isClassroomFull,
    required this.startDate,
    required this.attendancePercentage,
    required this.id,
    required this.supervisorInAcademicBranchModel,
  });

  factory BatchesCoursesDetailsModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return BatchesCoursesDetailsModel(
      batchName: json['batch_name'] as String?,
      isClassroomFull: json['is_classroom_full'] as int?,
      startDate: json['start_date'] as String?,
      attendancePercentage: json['attendance_percentage'] as int?,
      id: json['batch_id'] as int?,
      supervisorInAcademicBranchModel: json['supervisor'] != null
          ? SupervisorCoursesDetailsModel.fromJson(json: json['supervisor'])
          : null,
    );
  }
}
