class PaymentModel {
  final String? financialInvoice;
  final num? amount;
  final String? date;
  PaymentModel({
    required this.financialInvoice,
    required this.amount,
    required this.date,
  });
  factory PaymentModel.fromJson({required Map<String, dynamic> json}) {
    return PaymentModel(
      financialInvoice: json['receipt_number'] as String?,
      amount: json['amount_usd'] as num?,
      date: json['paid_date'] as String?,
    );
  }
}
