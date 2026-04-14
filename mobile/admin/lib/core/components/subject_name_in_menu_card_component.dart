import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class SubjectNameInMenuCardComponent extends StatelessWidget {
  const SubjectNameInMenuCardComponent({super.key, required this.subjectName});
  final String subjectName;
  @override
  Widget build(BuildContext context) {
    return TextMedium14Component(
      text: subjectName,
      color: ColorsStyle.mediumBrownColor,
    );
  }
}
