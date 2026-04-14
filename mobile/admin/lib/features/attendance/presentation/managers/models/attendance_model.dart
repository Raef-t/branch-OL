class AttendanceModel {
  final String? date;
  final String? checkIn;
  final String? checkOut;
  final String? status;
  final String? day;
  AttendanceModel({
    required this.date,
    required this.checkIn,
    required this.checkOut,
    required this.status,
    required this.day,
  });
  factory AttendanceModel.fromJson({required Map<String, dynamic> json}) {
    return AttendanceModel(
      date: json['date'] as String?,
      checkIn: json['check_in'] as String?,
      checkOut: json['check_out'] as String?,
      status: json['status'] as String?,
      day: json['day'] as String?,
    );
  }
}
