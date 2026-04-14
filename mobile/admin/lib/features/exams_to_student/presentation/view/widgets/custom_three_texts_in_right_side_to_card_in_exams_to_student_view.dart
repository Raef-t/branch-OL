import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomThreeTextsInRightSideToCardInExamsToStudentView
    extends StatelessWidget {
  const CustomThreeTextsInRightSideToCardInExamsToStudentView({
    super.key,
    required this.date,
    required this.course,
    required this.classRoom,
  });
  final String date, course, classRoom;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        TextMedium12Component(
          text: date,
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBrownColor,
        ),
        Heights.height9(context: context),
        TextMedium12Component(
          text: course,
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBrownColor,
        ),
        Heights.height9(context: context),
        TextMedium12Component(
          text: classRoom,
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBrownColor,
        ),
      ],
    );
  }
}
