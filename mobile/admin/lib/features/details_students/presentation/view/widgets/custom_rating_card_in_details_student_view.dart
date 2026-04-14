import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/details_students/presentation/managers/cubits/monthly_evaluations/monthly_evaluations_cubit.dart';
import '/features/details_students/presentation/managers/cubits/monthly_evaluations/monthly_evaluations_state.dart';
import '/features/details_students/presentation/view/widgets/custom_contain_rating_card_in_details_student_view.dart';

class CustomRatingCardInDetailsStudentView extends StatelessWidget {
  const CustomRatingCardInDetailsStudentView({
    super.key,
    required this.selectedValue,
    required this.maxRating,
    required this.onSelected,
  });
  final String selectedValue;
  final double maxRating;
  final void Function(String) onSelected;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<MonthlyEvaluationCubit, MonthlyEvaluationState>(
      builder: (context, state) {
        if (state is MonthlyEvaluationSuccess) {
          final rating = state.evaluations.map((e) => e.rating ?? 0).toList();
          return Container(
            margin: OnlyPaddingWithoutChild.left21AndRight20(context: context),
            padding: OnlyPaddingWithoutChild.left19AndTop13AndRight7AndBottom2(
              context: context,
            ),
            decoration:
                BoxDecorations.boxDecorationToRatingCardInDetailsStudentView(
                  context: context,
                ),
            child: CustomContainRatingCardInDetailsStudentView(
              selectedValue: selectedValue,
              maxRating: maxRating,
              onSelected: onSelected,
              ratings: rating,
            ),
          );
        } else if (state is MonthlyEvaluationFailure) {
          return FailureStateComponent(
            errorText: state.errorMessage,
            onPressed: () =>
                context.read<MonthlyEvaluationCubit>().getMonthlyEvaluations(),
          );
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
