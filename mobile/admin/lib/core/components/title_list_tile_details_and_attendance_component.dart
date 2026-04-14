import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class TitleListTileDetialsAndAttendanceComponent extends StatelessWidget {
  const TitleListTileDetialsAndAttendanceComponent({
    super.key,
    required this.studentName,
  });
  final String studentName;
  @override
  Widget build(BuildContext context) {
    return TextMedium14Component(
      text: studentName,
      color: ColorsStyle.veryLittleBlackColor,
    );
  }
}
