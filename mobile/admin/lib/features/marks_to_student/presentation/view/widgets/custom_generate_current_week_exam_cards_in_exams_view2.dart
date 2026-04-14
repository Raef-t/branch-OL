import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/details_students/presentation/managers/cubits/mark_students/mark_students_cubit.dart';
import '/features/details_students/presentation/managers/cubits/mark_students/mark_students_state.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_success_state_current_marks_in_marks_view.dart';

class CustomGenerateCurrentWeekExamCardsInExamsView2 extends StatelessWidget {
  const CustomGenerateCurrentWeekExamCardsInExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<MarkStudentsCubit, MarkStudentsState>(
      builder: (context, state) {
        if (state is MarkStudentsSuccessState) {
          final currentWeeksList =
              state.markTwoWeeksModelInCubit.currentWeekList;
          final length = currentWeeksList.length;
          if (currentWeeksList.isEmpty) {
            return const TextSuccessStateButTheDataIsEmptyComponent(
              text: 'لا يوجد علامات',
            );
          }
          return CustomSuccessStateCurrentMarksInMarksView(
            length: length,
            currentWeeksList: currentWeeksList,
          );
        } else if (state is MarkStudentsFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<MarkStudentsCubit>().getLastTwoWeeksExams(),
          );
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
