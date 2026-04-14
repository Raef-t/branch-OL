import 'package:flutter/material.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_leading_list_tile_in_payments_view.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_subtilte_list_tile_in_payments_view.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_title_list_tile_in_payments_view.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_trailing_list_tile_in_payments_view.dart';

class CustomListInPaymentsView extends StatelessWidget {
  const CustomListInPaymentsView({
    super.key,
    required this.color,
    required this.title,
    required this.date,
    required this.financialInvoice,
    required this.amount,
    required this.paymentType,
  });
  final Color color;
  final String title, date, financialInvoice, amount, paymentType;
  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: CustomLeadingListTileInPaymentsView(color: color),
      title: CustomTitleListTileInPaymentsView(title: title),
      subtitle: CustomSubtilteListTileInPaymentsView(
        date: date,
        financialInvoice: financialInvoice,
        paymentType: paymentType,
      ),
      trailing: CustomTrailingListTileInPaymentsView(amount: amount),
    );
  }
}
