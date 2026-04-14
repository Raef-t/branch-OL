import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_text_field_to_mark_exam_card_in_filter_exams_view2.dart';

class CustomMarkExamCardInFilterExamsView2 extends StatelessWidget {
  const CustomMarkExamCardInFilterExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Container(
      width: size.width * 0.148,
      height: size.height * (isRotait ? 0.046 : 0.07),
      alignment: Alignment.center,
      decoration: BoxDecorations.boxDecorationToMarkExamCardInFilterExamsView2(
        context: context,
      ),
      child: const CustomTextFieldToMarkExamCardInFilterExamsView2(),
    );
  }
}
