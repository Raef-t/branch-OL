import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

abstract class InputDecorations {
  static InputDecoration inputDecorationSearchTextFieldComponent({
    required BuildContext context,
  }) {
    return InputDecoration(
      hintTextDirection: TextDirection.rtl,
      fillColor: ColorsStyle.littleGreyColor,
      filled: true,
      border: InputBorder.none,
      hintText: 'بحث',
      suffixIcon: Assets.images.iconSearchImage.image(),
      hintStyle: TextsStyle.normal14(context: context),
    );
  }

  static InputDecoration inputDecorationToMarkExamCardInFilterExamsView2({
    required BuildContext context,
  }) {
    return InputDecoration(
      filled: true,
      fillColor: ColorsStyle.transparentColor,
      isDense: true,
      //put the content in the center in this platform(i mean when you in left so the content will be in the centerLeft)
      border: InputBorder.none,
      hintText: '200',
      hintStyle: TextsStyle.medium12(
        context: context,
      ).copyWith(fontFamily: FontFamily.tajawal, color: ColorsStyle.blackColor),
    );
  }
}
