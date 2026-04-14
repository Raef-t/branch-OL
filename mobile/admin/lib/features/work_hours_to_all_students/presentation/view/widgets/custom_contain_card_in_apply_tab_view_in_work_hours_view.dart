import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class CustomContainCardInApplyTabViewInWorkHoursView extends StatelessWidget {
  const CustomContainCardInApplyTabViewInWorkHoursView({super.key});

  @override
  Widget build(BuildContext context) {
    return const Column(
      children: [
        TextMedium14Component(
          text: 'رياضيات',
          color: ColorsStyle.mediumBrownColor,
        ),
        TextMedium14Component(text: 'قاعة 5', color: ColorsStyle.redColor),
      ],
    );
  }
}
