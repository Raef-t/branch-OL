import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/components/text_medium16_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomDateTextsInsideDetailsCardInCoursesDetailsView
    extends StatelessWidget {
  const CustomDateTextsInsideDetailsCardInCoursesDetailsView({
    super.key,
    required this.date,
  });
  final String date;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        TextMedium16Component(
          text: date,
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.greyColor,
        ),
        Widths.width8(context: context),
        const TextMedium14Component(
          text: 'تاريخ البدء',
          color: ColorsStyle.greyColor,
        ),
      ],
    );
  }
}
