import 'package:flutter/material.dart';
import '/core/components/vertical_divider_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_exam_card_in_exam_view.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_two_time_texts_in_time_card_exam_view.dart';

class CustomExamAndDividerAndTimeCardsInExamView extends StatelessWidget {
  const CustomExamAndDividerAndTimeCardsInExamView({
    super.key,
    required this.examsModel,
    required this.index,
    required this.subjectColor,
  });
  final ExamsModel examsModel;
  final int index;
  final Color subjectColor;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left26AndRight25(
      context: context,
      child: IntrinsicHeight(
        child: Container(
          color: ColorsStyle.backSection,

          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                flex: 5,
                child: CustomExamCardInExamView(
                  examsModel: examsModel,
                  subjectColor: subjectColor,
                ),
              ),
              Widths.width17(context: context),
              const VerticalDividerComponent(
                color: ColorsStyle.veryLittleGreyColor,
                thickness: 1,
                width: 1,
              ),
              Widths.width10(context: context),
              Expanded(
                child: CustomTwoTimeTextsInTimeCardExamView(
                  examsModel: examsModel,
                  index: index,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
