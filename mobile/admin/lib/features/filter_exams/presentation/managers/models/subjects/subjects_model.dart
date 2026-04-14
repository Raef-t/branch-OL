class SubjectsModel {
  final int? id;
  final String? subjectName;
  SubjectsModel({required this.id, required this.subjectName});
  factory SubjectsModel.fromJson({required Map<String, dynamic> json}) {
    return SubjectsModel(
      id: json['id'] as int?,
      subjectName: json['name'] as String?,
    );
  }
}
