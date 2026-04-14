class ClassRoomModel {
  final String? classRoom;
  ClassRoomModel({required this.classRoom});
  factory ClassRoomModel.fromJson({required Map<String, dynamic> json}) {
    return ClassRoomModel(classRoom: (json['name'] ?? json['code']) as String?);
  }
}
