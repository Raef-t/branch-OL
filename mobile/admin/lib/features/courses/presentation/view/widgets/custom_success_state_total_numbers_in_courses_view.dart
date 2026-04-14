import 'package:flutter/material.dart';
import '/core/components/text_medium12_component.dart';
import '/core/components/text_medium32_component.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomSuccessStateTotalNumbersInCoursesView extends StatelessWidget {
  const CustomSuccessStateTotalNumbersInCoursesView({
    super.key,
    required this.totalStudents,
  });
  final int totalStudents;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        TextMedium32Component(text: totalStudents.toString()),
        const TextMedium12Component(
          text: 'إجمالي عدد الطلاب',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.greyColor,
        ),
        const TextMedium12Component(
          text: 'في الشعب',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.greyColor,
        ),
      ],
    );
  }
}
