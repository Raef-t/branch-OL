class SupervisorInClassScheduleModel {
  final String? nameSupervisor;
  final String? photoSupervisor;

  SupervisorInClassScheduleModel({
    required this.nameSupervisor,
    required this.photoSupervisor,
  });

  factory SupervisorInClassScheduleModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return SupervisorInClassScheduleModel(
      nameSupervisor: json['name'],
      photoSupervisor: json['photo'],
    );
  }
}
