import 'package:flutter/material.dart';
import '/core/components/exams_card_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/features/details_students/presentation/managers/models/marks_student/exam_result_model.dart';

class CustomSuccessStateCurrentMarksInMarksView extends StatelessWidget {
  const CustomSuccessStateCurrentMarksInMarksView({
    super.key,
    required this.length,
    required this.currentWeeksList,
  });
  final int length;
  final List<ExamResultModel> currentWeeksList;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(length, (index) {
        final examResultModel = currentWeeksList[index];
        return OnlyPaddingWithChild.left18AndRight22AndBottom10(
          context: context,
          child: ExamsCardComponent(examResultModel: examResultModel),
        );
      }),
    );
  }
}
