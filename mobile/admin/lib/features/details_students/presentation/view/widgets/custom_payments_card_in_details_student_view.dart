import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/features/details_students/presentation/view/widgets/custom_contain_payments_card_in_details_student_view.dart';

class CustomPaymentsCardInDetailsStudentView extends StatelessWidget {
  const CustomPaymentsCardInDetailsStudentView({
    super.key,
    required this.lastElementInListOfPaymentModel,
  });
  final PaymentModel lastElementInListOfPaymentModel;
  @override
  Widget build(BuildContext context) {
    return Card(
      color: ColorsStyle.whiteColor,
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: Circulars.circular10(context: context),
      ),
      child: CustomContainPaymentsCardInDetailsStudentView(
        lastElementInListOfPaymentModel: lastElementInListOfPaymentModel,
      ),
    );
  }
}
