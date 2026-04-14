class SupervisorCoursesDetailsModel {
  final String? nameSupervisor;
  final String? photoSupervisor;
  SupervisorCoursesDetailsModel({this.nameSupervisor, this.photoSupervisor});
  factory SupervisorCoursesDetailsModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return SupervisorCoursesDetailsModel(
      nameSupervisor: json['name'] as String?,
      photoSupervisor: json['photo'] as String?,
    );
  }
}
