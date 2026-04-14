import 'package:flutter/material.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/features/details_students/presentation/view/widgets/custom_payments_list_tile_in_details_student_view.dart';

class CustomContainPaymentsCardInDetailsStudentView extends StatelessWidget {
  const CustomContainPaymentsCardInDetailsStudentView({
    super.key,
    required this.lastElementInListOfPaymentModel,
  });
  final PaymentModel lastElementInListOfPaymentModel;
  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: CustomPaymentsListTileInDetailsStudentView(
        lastElementInListOfPaymentModel: lastElementInListOfPaymentModel,
      ),
    );
  }
}
