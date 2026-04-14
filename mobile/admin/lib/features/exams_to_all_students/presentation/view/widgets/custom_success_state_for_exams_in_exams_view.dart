import 'package:flutter/material.dart';
import '/core/lists/color_to_subject_name_in_exams_view_list.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_state.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_exam_and_divider_and_time_cards_in_exam_view.dart';

class CustomSuccessStateForExamsInExamsView extends StatelessWidget {
  const CustomSuccessStateForExamsInExamsView({
    super.key,
    required this.length,
    required this.state,
  });
  final int length;
  final ExamsSuccessState state;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(length, (index) {
        final examsModel = state.examsListInCubit[index];
        final color =
            colorToSubjectNameInExamsViewList[index %
                colorToSubjectNameInExamsViewList.length];
        return CustomExamAndDividerAndTimeCardsInExamView(
          examsModel: examsModel,
          index: index,
          subjectColor: color,
        );
      }),
    );
  }
}
