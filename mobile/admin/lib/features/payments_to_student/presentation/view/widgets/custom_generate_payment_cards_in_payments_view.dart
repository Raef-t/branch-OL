import 'package:flutter/material.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/features/details_students/presentation/managers/models/financial_summary/pending_installment_model.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_generate_payment_done_in_payments_view.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_payment_not_done_in_payments_view.dart';

class CustomGeneratePaymentCardsInPaymentsView extends StatelessWidget {
  const CustomGeneratePaymentCardsInPaymentsView({
    super.key,
    required this.listOfPaymentModel,
    required this.listOfPendingInstallmentModel,
  });
  final List<PaymentModel>? listOfPaymentModel;
  final List<PendingInstallmentModel>? listOfPendingInstallmentModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        CustomGeneratePaymentDoneInPaymentsView(
          listOfPaymentModel: listOfPaymentModel,
        ),
        CustomPaymentNotDoneInPaymentsView(
          listOfPendingInstallmentModel: listOfPendingInstallmentModel,
          paymentType: 'red',
        ),
      ],
    );
  }
}
