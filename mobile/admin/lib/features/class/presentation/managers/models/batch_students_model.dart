class BatchStudentsModel {
  final String? fullName;
  final String? profilePhoto;
  final String? phone;
  final String? date;
  final int? id;
  final num? remainingAmount;
  final bool? attendance;
  BatchStudentsModel({
    required this.fullName,
    required this.profilePhoto,
    required this.phone,
    required this.date,
    required this.id,
    required this.remainingAmount,
    required this.attendance,
  });
  factory BatchStudentsModel.fromJson({required Map<String, dynamic> json}) {
    return BatchStudentsModel(
      fullName: json['full_name'] as String?,
      profilePhoto: json['profile_photo_url'] as String?,
      phone: json['primary_phone'] as String?,
      date: json['attendance_enrolment'] as String?,
      id: json['id'] as int?,
      remainingAmount: json['remaining_amount_usd'] as num?,
      attendance: json['attended_today'] as bool?,
    );
  }
}
