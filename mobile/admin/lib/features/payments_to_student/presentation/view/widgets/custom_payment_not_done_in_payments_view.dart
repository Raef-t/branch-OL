import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/financial_summary/pending_installment_model.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_payment_card_in_payments_view.dart';

class CustomPaymentNotDoneInPaymentsView extends StatelessWidget {
  const CustomPaymentNotDoneInPaymentsView({
    super.key,
    this.listOfPendingInstallmentModel,
    required this.paymentType,
  });
  final List<PendingInstallmentModel>? listOfPendingInstallmentModel;
  final String paymentType;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(listOfPendingInstallmentModel?.length ?? 0, (
        index,
      ) {
        final pendingInstallmentModel = listOfPendingInstallmentModel?[index];
        return CustomPaymentCardInPaymentsView(
          color:
              (pendingInstallmentModel?.paidStatus ?? 'تم الدفع') == 'pending'
              ? ColorsStyle.redColor
              : ColorsStyle.greenColor2,
          title:
              'الدفعة '
              '${index + 1}',
          date: pendingInstallmentModel?.date ?? 'لا يوجد تاريخ',
          financialInvoice: 'لا يوجد إيصال',
          amount: (pendingInstallmentModel?.amount.toString()) ?? '0',
          paymentType: paymentType,
        );
      }),
    );
  }
}
