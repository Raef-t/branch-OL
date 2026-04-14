class DoorSessionModel {
  final String? token;
  DoorSessionModel({required this.token});
  factory DoorSessionModel.fromJson({required Map<String, dynamic> json}) {
    return DoorSessionModel(token: json['token'] as String?);
  }
}
