import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/styles/colors_style.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_contain_payment_card_in_payments_view.dart';

class CustomPaymentCardInPaymentsView extends StatelessWidget {
  const CustomPaymentCardInPaymentsView({
    super.key,
    required this.color,
    required this.title,
    required this.date,
    required this.financialInvoice,
    required this.amount,
    required this.paymentType,
  });
  final Color color;
  final String title, date, financialInvoice, amount;
  final String paymentType;
  @override
  Widget build(BuildContext context) {
    return Card(
      color: ColorsStyle.mediumWhiteColor,
      elevation: 0,
      margin: OnlyPaddingWithoutChild.left20AndRight20AndBottom14(
        context: context,
      ),
      shape: RoundedRectangleBorder(
        borderRadius: Circulars.circular10(context: context),
      ),
      child: CustomContainPaymentCardInPaymentsView(
        title: title,
        color: color,
        date: date,
        financialInvoice: financialInvoice,
        amount: amount,
        paymentType: paymentType,
      ),
    );
  }
}
