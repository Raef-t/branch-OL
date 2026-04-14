import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class CustomTextWithPaddingInFilterExamsView2 extends StatelessWidget {
  const CustomTextWithPaddingInFilterExamsView2({
    super.key,
    required this.text,
  });
  final String text;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right22(
      context: context,
      child: Align(
        alignment: Alignment.centerRight,
        child: TextMedium16Component(
          text: text,
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBrownColor,
        ),
      ),
    );
  }
}
