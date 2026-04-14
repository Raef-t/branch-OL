import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class CustomContainSubjectCardInFilterExamsView2 extends StatelessWidget {
  const CustomContainSubjectCardInFilterExamsView2({
    super.key,
    required this.subjectName,
    required this.selectedSubjectCard,
    required this.index,
  });
  final String subjectName;
  final int selectedSubjectCard, index;
  @override
  Widget build(BuildContext context) {
    return FittedBox(
      child: TextMedium14Component(
        text: subjectName,
        color: selectedSubjectCard == index
            ? ColorsStyle.littleVinicColor
            : ColorsStyle.mediumBrownColor,
      ),
    );
  }
}
