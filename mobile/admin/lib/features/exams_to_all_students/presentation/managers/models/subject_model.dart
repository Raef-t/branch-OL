class SubjectModel {
  final String? subjectName;
  SubjectModel({required this.subjectName});
  factory SubjectModel.fromJson({required Map<String, dynamic> json}) {
    return SubjectModel(subjectName: json['name'] as String?);
  }
}
