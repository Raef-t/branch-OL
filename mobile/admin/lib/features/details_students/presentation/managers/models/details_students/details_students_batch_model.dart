class DetailsStudentsBatchModel {
  final String? batchName;
  DetailsStudentsBatchModel({required this.batchName});
  factory DetailsStudentsBatchModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return DetailsStudentsBatchModel(batchName: json['name'] as String?);
  }
}
