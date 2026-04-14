class AcademicBranchesToCoursesModel {
  final String? courseName;
  final int? studentsCount;
  final int? batchesCount;
  final int? id;
  AcademicBranchesToCoursesModel({
    required this.courseName,
    required this.studentsCount,
    required this.batchesCount,
    required this.id,
  });
  factory AcademicBranchesToCoursesModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return AcademicBranchesToCoursesModel(
      courseName: json['academic_branch_name'] as String?,
      studentsCount: json['students_count'] as int?,
      batchesCount: json['batches_count'] as int?,
      id: json['academic_branch_id'] as int?,
    );
  }
}
