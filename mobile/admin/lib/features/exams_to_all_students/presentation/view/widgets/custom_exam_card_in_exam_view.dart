import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_contain_exam_card_in_exam_view.dart';

class CustomExamCardInExamView extends StatelessWidget {
  const CustomExamCardInExamView({
    super.key,
    required this.examsModel,
    required this.subjectColor,
  });
  final ExamsModel examsModel;
  final Color subjectColor;
  @override
  Widget build(BuildContext context) {
    return Container(
      margin: OnlyPaddingWithoutChild.bottom21(context: context),
      padding: const EdgeInsets.only(top: 9, bottom: 4, left: 19, right: 10),
      decoration: BoxDecorations.boxDecorationExamsCardInExamView(
        context: context,
      ),
      child: CustomContainExamCardInExamView(
        examsModel: examsModel,
        subjectColor: subjectColor,
      ),
    );
  }
}
