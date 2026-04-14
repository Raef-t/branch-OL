class UserModel {
  final int? id;
  final String? name;
  final String? photo;
  UserModel({required this.name, required this.photo, required this.id});
  factory UserModel.fromJson({required Map<String, dynamic> json}) {
    return UserModel(
      id: json['id'] as int?,
      name: json['name'] as String?,
      photo: json['photo_url'] as String?,
    );
  }
}
