import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';

class AcademicBranchesCoursesDetailsModel {
  final String? courseName;
  final int? studentsCount;
  final int? batchesCount;
  final int? id;
  final List<BatchesCoursesDetailsModel>? listOfBtachesModel;

  AcademicBranchesCoursesDetailsModel({
    required this.courseName,
    required this.studentsCount,
    required this.batchesCount,
    required this.id,
    required this.listOfBtachesModel,
  });

  factory AcademicBranchesCoursesDetailsModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return AcademicBranchesCoursesDetailsModel(
      courseName: json['academic_branch_name'] as String?,
      studentsCount: json['students_count'] as int?,
      batchesCount: json['batches_count'] as int?,
      id: json['academic_branch_id'] as int?,
      listOfBtachesModel: json['batches'] != null
          ? (json['batches'] as List)
                .map((e) => BatchesCoursesDetailsModel.fromJson(json: e))
                .toList()
          : null,
    );
  }
}
