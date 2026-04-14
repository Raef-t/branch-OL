class ScanQrStudentModel {
  final String? fullName;
  final int? branchId;
  final int? instituteBranchId;
  ScanQrStudentModel({
    required this.fullName,
    required this.branchId,
    required this.instituteBranchId,
  });
  factory ScanQrStudentModel.fromJson({required Map<String, dynamic> json}) {
    return ScanQrStudentModel(
      fullName: json['full_name'] as String?,
      branchId: json['branch_id'] as int?,
      instituteBranchId: json['institute_branch_id'] as int?,
    );
  }
}
