class BatchModel {
  final String? course;
  BatchModel({required this.course});
  factory BatchModel.fromJson({required Map<String, dynamic> json}) {
    return BatchModel(course: json['name'] as String?);
  }
}
