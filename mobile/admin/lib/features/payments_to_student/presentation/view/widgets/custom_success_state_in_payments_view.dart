import 'package:flutter/material.dart';
import '/features/details_students/presentation/managers/cubits/financial_summary/financial_summary_state.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_sliver_app_bar_in_payments_view.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_sliver_fill_remaining_in_payments_view.dart';

class CustomSuccessStateInPaymentsView extends StatelessWidget {
  const CustomSuccessStateInPaymentsView({super.key, required this.state});
  final FinancialSummarySuccessState state;
  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        const CustomSliverAppBarInPaymentsView(),
        CustomSliverFillRemainingInPaymentsView(
          enrollmentContractModel:
              state.financialSummaryModelInCubit.enrollmentContractModel,
          listOfPaymentModel:
              state.financialSummaryModelInCubit.listOfPaymentModel,
          listOfPendingInstallmentModel:
              state.financialSummaryModelInCubit.listOfPendingInstallmentModel,
        ),
      ],
    );
  }
}
