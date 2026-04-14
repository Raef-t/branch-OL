class PendingInstallmentModel {
  final String? date;
  final num? amount;
  final String? paidStatus;
  PendingInstallmentModel({
    required this.date,
    required this.amount,
    required this.paidStatus,
  });
  factory PendingInstallmentModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    return PendingInstallmentModel(
      date: json['due_date'] as String?,
      amount: json['paid_amount_usd'] as num?,
      paidStatus: json['status'] as String?,
    );
  }
}
