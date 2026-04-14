import 'package:flutter/material.dart';
import '/core/components/see_more_text_and_another_text_component.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/gen/fonts.gen.dart';

class CustomSeeMoreTextWithAnotherTextInDetailsStudentView
    extends StatelessWidget {
  const CustomSeeMoreTextWithAnotherTextInDetailsStudentView({
    super.key,
    required this.text,
    required this.onTap,
  });
  final String text;
  final void Function() onTap;
  @override
  Widget build(BuildContext context) {
    return SeeMoreTextAndAnotherTextComponent(
      text: text,
      textStyleToAnotherText: TextsStyle.medium16(context: context).copyWith(
        fontFamily: FontFamily.tajawal,
        color: ColorsStyle.mediumBlackColor2,
      ),
      textStyleToSeeMoreText: TextsStyle.bold10(
        context: context,
      ).copyWith(color: ColorsStyle.littleVinicColor),
      onTap: onTap,
    );
  }
}
