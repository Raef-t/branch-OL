import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/failure_state_component.dart';
import '/features/exams_to_all_students/presentation/view/widgets/shimmer_exams_view_cards.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_cubit.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_state.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_success_state_for_exams_in_exams_view.dart';

class CustomGenerateExamAndTimeCardsInExamView extends StatelessWidget {
  const CustomGenerateExamAndTimeCardsInExamView({
    super.key,
    required this.onLengthExams,
  });
  final ValueChanged<int> onLengthExams;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<ExamsCubit, ExamsState>(
      builder: (context, state) {
        if (state is ExamsSuccessState) {
          final length = state.examsListInCubit.length;
          onLengthExams(length);
          if (length == 0) {
            return const TextSuccessStateButTheDataIsEmptyComponent(
              text: 'لا يوجد مذاكرات',
            );
          }
          return CustomSuccessStateForExamsInExamsView(
            length: length,
            state: state,
          );
        } else if (state is ExamsFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<ExamsCubit>().getExamsByDate(date: '2025-12-25'),
          );
        } else {
          return const ShimmerExamsViewCards();
          // return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
