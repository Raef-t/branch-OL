import 'package:flutter/material.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/features/details_students/presentation/view/widgets/custom_payments_card_in_details_student_view.dart';

class CustomSuccessStateForPaymentInDetailsStudentsView
    extends StatelessWidget {
  const CustomSuccessStateForPaymentInDetailsStudentsView({
    super.key,
    required this.lastElementInListOfPaymentModel,
  });
  final PaymentModel lastElementInListOfPaymentModel;
  @override
  Widget build(BuildContext context) {
    return CustomPaymentsCardInDetailsStudentView(
      lastElementInListOfPaymentModel: lastElementInListOfPaymentModel,
    );
  }
}
