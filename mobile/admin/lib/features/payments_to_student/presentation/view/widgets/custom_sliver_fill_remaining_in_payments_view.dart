import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/details_students/presentation/managers/models/financial_summary/enrollment_contract_model.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/features/details_students/presentation/managers/models/financial_summary/pending_installment_model.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_generate_payment_cards_in_payments_view.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_generate_rest_and_toatl_price_cards_in_payments_view.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_two_sale_texts_in_payments_view.dart';

class CustomSliverFillRemainingInPaymentsView extends StatelessWidget {
  const CustomSliverFillRemainingInPaymentsView({
    super.key,
    required this.enrollmentContractModel,
    required this.listOfPaymentModel,
    required this.listOfPendingInstallmentModel,
  });
  final EnrollmentContractModel? enrollmentContractModel;
  final List<PaymentModel>? listOfPaymentModel;
  final List<PendingInstallmentModel>? listOfPendingInstallmentModel;
  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: true,
      child: BackgroundBodyToViewsComponent(
        child: ListView(
          children: [
            Heights.height40(context: context),
            CustomTwoSaleTextsInPaymentsView(
              enrollmentContractModel: enrollmentContractModel,
            ),
            Heights.height24(context: context),
            CustomGenerateRestAndToatlPriceCardsInPaymentsView(
              enrollmentContractModel: enrollmentContractModel,
            ),
            Heights.height24(context: context),
            CustomGeneratePaymentCardsInPaymentsView(
              listOfPaymentModel: listOfPaymentModel,
              listOfPendingInstallmentModel: listOfPendingInstallmentModel,
            ),
          ],
        ),
      ),
    );
  }
}
