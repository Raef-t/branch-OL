import 'package:flutter/material.dart';
import '/core/components/text_medium14_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';

class CustomImageAndTextInBottomNavigationBarInClassView
    extends StatelessWidget {
  const CustomImageAndTextInBottomNavigationBarInClassView({
    super.key,
    required this.image,
    required this.text,
  });
  final Image image;
  final String text;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        image,
        Heights.height4(context: context),
        TextMedium14Component(text: text, color: ColorsStyle.mediumBlackColor2),
      ],
    );
  }
}
