import 'package:flutter/material.dart';
import '/features/class/presentation/view/widgets/custom_text_button_in_class_view.dart';

class CustomContainMessageCardInBottomNavigationBarInClassView
    extends StatelessWidget {
  const CustomContainMessageCardInBottomNavigationBarInClassView({super.key});

  @override
  Widget build(BuildContext context) {
    return const Column(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        CustomTextButtonInClassView(text: 'رسالة نصية', status: ''),
        CustomTextButtonInClassView(text: 'ملاحظة', status: ''),
      ],
    );
  }
}
