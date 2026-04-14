import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomTextUpFieldInAuthView extends StatelessWidget {
  const CustomTextUpFieldInAuthView({super.key, required this.text});
  final String text;
  @override
  Widget build(BuildContext context) {
    return TextMedium16Component(
      text: text,
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
