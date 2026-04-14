import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';

class CustomRightSideInsideNotificationCardInNotificationsView
    extends StatelessWidget {
  const CustomRightSideInsideNotificationCardInNotificationsView({super.key});

  @override
  Widget build(BuildContext context) {
    return const Expanded(
      flex: 4,
      child: TextMedium14Component(
        text: 'حصل الطالب أحمد على علامة قدرها 200 في مادة الرياضيات',
        color: ColorsStyle.littleBlackColor,
        textAlign: TextAlign.end,
      ),
    );
  }
}
