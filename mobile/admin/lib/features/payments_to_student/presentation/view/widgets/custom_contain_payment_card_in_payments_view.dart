import 'package:flutter/material.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_list_in_payments_view.dart';

class CustomContainPaymentCardInPaymentsView extends StatelessWidget {
  const CustomContainPaymentCardInPaymentsView({
    super.key,
    required this.title,
    required this.color,
    required this.date,
    required this.financialInvoice,
    required this.amount,
    required this.paymentType,
  });
  final String title, date, financialInvoice, amount, paymentType;
  final Color color;
  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: CustomListInPaymentsView(
        color: color,
        title: title,
        date: date,
        financialInvoice: financialInvoice,
        amount: amount,
        paymentType: paymentType,
      ),
    );
  }
}
