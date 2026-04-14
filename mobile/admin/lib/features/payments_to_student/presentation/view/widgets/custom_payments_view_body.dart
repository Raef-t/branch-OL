import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/features/details_students/presentation/managers/cubits/financial_summary/financial_summary_cubit.dart';
import '/features/details_students/presentation/managers/cubits/financial_summary/financial_summary_state.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_success_state_in_payments_view.dart';

class CustomPaymentsViewBody extends StatelessWidget {
  const CustomPaymentsViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<FinancialSummaryCubit, FinancialSummaryState>(
      builder: (context, state) {
        if (state is FinancialSummarySuccessState) {
          return CustomSuccessStateInPaymentsView(state: state);
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
    );
  }
}
