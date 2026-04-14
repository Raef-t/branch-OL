import 'package:flutter/material.dart';
import '/features/class/presentation/view/widgets/custom_text_button_in_class_view.dart';

class CustomContainTakePresenceCardInBottomNavigationBarInClassView
    extends StatelessWidget {
  const CustomContainTakePresenceCardInBottomNavigationBarInClassView({
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return const Column(
      children: [
        CustomTextButtonInClassView(text: 'موجود', status: 'موجود'),
        CustomTextButtonInClassView(text: 'غائب', status: 'غائب'),
        CustomTextButtonInClassView(text: 'متأخر', status: 'متأخر'),
        CustomTextButtonInClassView(text: 'إذن', status: 'إذن'),
      ],
    );
  }
}
