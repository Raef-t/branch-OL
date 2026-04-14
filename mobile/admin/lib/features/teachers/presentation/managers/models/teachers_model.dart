class TeachersModel {
  final String? name;
  final String? specialization;
  final String? photo;
  TeachersModel({
    required this.name,
    required this.specialization,
    required this.photo,
  });
  factory TeachersModel.fromJson({required Map<String, dynamic> json}) {
    return TeachersModel(
      name: json['name'] as String?,
      specialization: json['specialization'] as String?,
      photo: json['profile_photo_url'] as String?,
    );
  }
}
