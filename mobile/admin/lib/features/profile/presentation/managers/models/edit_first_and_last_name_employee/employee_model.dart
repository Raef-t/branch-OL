class EmployeeModel {
  final String? photoUrl;
  EmployeeModel({required this.photoUrl});
  factory EmployeeModel.fromJson({required Map<String, dynamic> json}) {
    return EmployeeModel(photoUrl: json['photo_url'] as String?);
  }
}
