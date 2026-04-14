import 'package:flutter/material.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/details_students/presentation/managers/models/financial_summary/enrollment_contract_model.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_rest_and_total_price_card_in_payments_view.dart';

class CustomGenerateRestAndToatlPriceCardsInPaymentsView
    extends StatelessWidget {
  const CustomGenerateRestAndToatlPriceCardsInPaymentsView({
    super.key,
    required this.enrollmentContractModel,
  });
  final EnrollmentContractModel? enrollmentContractModel;
  @override
  Widget build(BuildContext context) {
    return IntrinsicHeight(
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          CustomRestAndTotalPriceCardInPaymentsView(
            firstText: 'المتبقي',
            color: ColorsStyle.veryLittleSkyBlueColor,
            secondText: (enrollmentContractModel?.remainingAmount ?? 0)
                .toString(),
          ),
          Widths.width27(context: context),
          CustomRestAndTotalPriceCardInPaymentsView(
            firstText: 'المجموع الكلي',
            color: ColorsStyle.veryLittlePinkColor,
            secondText:
                (enrollmentContractModel?.totalAmount.toString()) ?? '0',
          ),
        ],
      ),
    );
  }
}
