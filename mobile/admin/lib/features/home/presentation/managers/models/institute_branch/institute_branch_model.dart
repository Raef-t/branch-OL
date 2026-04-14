class InstituteBranchModel {
  final String? name;
  final int? id;
  InstituteBranchModel({required this.name, required this.id});
  factory InstituteBranchModel.fromJson({required Map<String, dynamic> json}) {
    return InstituteBranchModel(
      name: json['name'] as String?,
      id: json['id'] as int?,
    );
  }
}
