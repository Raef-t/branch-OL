import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class CustomTitlePaymentsListTileInDetailsStudentView extends StatelessWidget {
  const CustomTitlePaymentsListTileInDetailsStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    return const TextMedium14Component(
      text: 'اخر دفعه تم دفعها',
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
