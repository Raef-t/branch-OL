import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_payment_card_in_payments_view.dart';

class CustomGeneratePaymentDoneInPaymentsView extends StatelessWidget {
  const CustomGeneratePaymentDoneInPaymentsView({
    super.key,
    this.listOfPaymentModel,
  });
  final List<PaymentModel>? listOfPaymentModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(listOfPaymentModel?.length ?? 0, (index) {
        final paymentModel = listOfPaymentModel?[index];
        return CustomPaymentCardInPaymentsView(
          color: ColorsStyle.greenColor2,
          paymentType: 'green',
          title:
              'الدفعة '
              '${index + 1}',
          date: paymentModel?.date ?? 'لا يوجد تاريخ',
          financialInvoice: paymentModel?.financialInvoice ?? 'لا يوجد إيصال',
          amount: (paymentModel?.amount.toString()) ?? '0',
        );
      }),
    );
  }
}
