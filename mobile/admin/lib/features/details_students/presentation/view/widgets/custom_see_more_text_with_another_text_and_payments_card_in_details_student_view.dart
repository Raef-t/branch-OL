import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/heights.dart';
import '/features/details_students/presentation/managers/cubits/financial_summary/financial_summary_cubit.dart';
import '/features/details_students/presentation/managers/cubits/financial_summary/financial_summary_state.dart';
import '/features/details_students/presentation/view/widgets/custom_see_more_text_with_another_text_to_payments_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_success_state_for_payment_in_details_students_view.dart';

class CustomSeeMoreTextWithAnotherTextAndPaymentsCardInDetailsStudentView
    extends StatelessWidget {
  const CustomSeeMoreTextWithAnotherTextAndPaymentsCardInDetailsStudentView({
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left25AndRight15(
      context: context,
      child: Column(
        children: [
          CustomSeeMoreTextWithAnotherTextInDetailsStudentView(
            text: 'الدفعات',
            onTap: () =>
                pushGoRouterHelper(context: context, view: kPaymentsViewRouter),
          ),
          Heights.height16(context: context),
          BlocBuilder<FinancialSummaryCubit, FinancialSummaryState>(
            builder: (context, state) {
              if (state is FinancialSummarySuccessState) {
                final listOfPaymentModel =
                    state.financialSummaryModelInCubit.listOfPaymentModel;
                if (listOfPaymentModel == null || listOfPaymentModel.isEmpty) {
                  return const TextSuccessStateButTheDataIsEmptyComponent(
                    text: 'لا يوجد دفعه قد تم دفعها',
                  );
                }
                final lastElementInListOfPaymentModel = listOfPaymentModel.last;
                return CustomSuccessStateForPaymentInDetailsStudentsView(
                  lastElementInListOfPaymentModel:
                      lastElementInListOfPaymentModel,
                );
              } else if (state is FinancialSummaryFailureState) {
                return FailureStateComponent(
                  errorText: state.errorMessageInCubit,
                  onPressed: () => context
                      .read<FinancialSummaryCubit>()
                      .getStudentFinancialSummary(),
                );
              } else {
                return const CircleLoadingStateComponent();
              }
            },
          ),
        ],
      ),
    );
  }
}
