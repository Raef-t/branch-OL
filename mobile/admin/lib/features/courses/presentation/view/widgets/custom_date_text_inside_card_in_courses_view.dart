import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/components/text_medium14_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomDateTextInsideCardInCoursesView extends StatelessWidget {
  const CustomDateTextInsideCardInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        const TextMedium14Component(
          text: '1/2/2025',
          color: ColorsStyle.greyColor,
        ),
        Widths.width5(context: context),
        const TextMedium12Component(
          text: 'تاريخ البدء',
          color: ColorsStyle.greyColor,
          fontFamily: FontFamily.tajawal,
        ),
      ],
    );
  }
}
