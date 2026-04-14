class TotalStudentsModel {
  final int? totalStudents;
  TotalStudentsModel({required this.totalStudents});
  factory TotalStudentsModel.fromJson({required Map<String, dynamic> json}) {
    return TotalStudentsModel(totalStudents: json['total_students'] as int?);
  }
}
