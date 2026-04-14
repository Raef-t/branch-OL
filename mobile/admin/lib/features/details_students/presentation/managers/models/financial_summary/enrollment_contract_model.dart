class EnrollmentContractModel {
  final num? totalAmount;
  final num? remainingAmount;
  final num? discountPercentage;
  EnrollmentContractModel({
    required this.totalAmount,
    required this.remainingAmount,
    required this.discountPercentage,
  });
  factory EnrollmentContractModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return EnrollmentContractModel(
      totalAmount: json['total_amount_usd'] as num?,
      remainingAmount: json['remaining_amount_usd'] as num?,
      discountPercentage: json['discount_percentage'] as num?,
    );
  }
}
