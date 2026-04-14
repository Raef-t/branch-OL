import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_mark_exam_card_in_filter_exams_view2.dart';

class CustomMarkExamCardWithTextUpItInFilterExamsView2 extends StatelessWidget {
  const CustomMarkExamCardWithTextUpItInFilterExamsView2({
    super.key,
    required this.text,
  });
  final String text;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        TextMedium12Component(text: text, color: ColorsStyle.greyColor),
        Heights.height11(context: context),
        const CustomMarkExamCardInFilterExamsView2(),
      ],
    );
  }
}
