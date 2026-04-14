import 'package:flutter/widgets.dart';
import '/core/helpers/responsive_text_helper.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

abstract class TextsStyle {
  static TextStyle normal10({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.inter,
      fontWeight: FontWeight.w400,
      fontSize: responsiveTextHelper(fontSize: 10, context: context),
      color: ColorsStyle.mediumBrownColor,
    );
  }

  static TextStyle semiBold10({required BuildContext context}) {
    return TextStyle(
      fontWeight: FontWeight.w600,
      fontSize: responsiveTextHelper(fontSize: 10, context: context),
      color: ColorsStyle.mediumRussetColor2,
      fontFamily: FontFamily.inter,
    );
  }

  static TextStyle bold10({required BuildContext context}) {
    return TextStyle(
      fontWeight: FontWeight.w700,
      fontSize: responsiveTextHelper(fontSize: 10, context: context),
      color: ColorsStyle.mediumRussetColor2,
      fontFamily: FontFamily.tajawal,
    );
  }

  static TextStyle medium10({required BuildContext context}) {
    return TextStyle(
      fontWeight: FontWeight.w500,
      fontSize: responsiveTextHelper(fontSize: 10, context: context),
      fontFamily: FontFamily.inter,
      color: ColorsStyle.littleBrownColor,
    );
  }

  static TextStyle medium11({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.inter,
      fontWeight: FontWeight.w500,
      fontSize: responsiveTextHelper(fontSize: 11, context: context),
      color: ColorsStyle.blackColor,
    );
  }

  static TextStyle normal12({required BuildContext context}) {
    return TextStyle(
      fontSize: responsiveTextHelper(fontSize: 12, context: context),
      fontWeight: FontWeight.w400,
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.mediumBrownColor,
    );
  }

  static TextStyle medium12({required BuildContext context}) {
    return TextStyle(
      fontSize: responsiveTextHelper(fontSize: 15, context: context),
      fontWeight: FontWeight.w500,
      fontFamily: FontFamily.poppins,
      color: ColorsStyle.littleBlackColor,
    );
  }

  static TextStyle bold12({required BuildContext context}) {
    return TextStyle(
      fontWeight: FontWeight.w700,
      fontSize: responsiveTextHelper(fontSize: 15, context: context),
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.blackColor,
    );
  }

  static TextStyle normal13({required BuildContext context}) {
    return TextStyle(
      color: ColorsStyle.mediumBrownColor,
      fontSize: responsiveTextHelper(fontSize: 13, context: context),
      fontFamily: FontFamily.inter,
      fontWeight: FontWeight.w400,
    );
  }

  static TextStyle medium13({required BuildContext context}) {
    return TextStyle(
      fontWeight: FontWeight.w500,
      fontSize: responsiveTextHelper(fontSize: 13, context: context),
      fontFamily: FontFamily.inter,
      color: ColorsStyle.blackColor,
    );
  }

  static TextStyle bold13({required BuildContext context}) {
    return TextStyle(
      fontWeight: FontWeight.w700,
      fontSize: responsiveTextHelper(fontSize: 13, context: context),
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.littleBlackColor,
    );
  }

  static TextStyle normal14({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.inter,
      fontWeight: FontWeight.w400,
      fontSize: responsiveTextHelper(fontSize: 14, context: context),
      color: ColorsStyle.mediumBrownColor,
    );
  }

  static TextStyle medium14({required BuildContext context}) {
    return TextStyle(
      fontWeight: FontWeight.w500,
      fontSize: responsiveTextHelper(fontSize: 14, context: context),
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.blackColor,
    );
  }

  static TextStyle bold14({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.inter,
      fontWeight: FontWeight.w700,
      fontSize: responsiveTextHelper(fontSize: 14, context: context),
      color: ColorsStyle.mediumBrownColor,
    );
  }

  static TextStyle medium15({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.inter,
      fontWeight: FontWeight.w500,
      fontSize: responsiveTextHelper(fontSize: 15, context: context),
      color: ColorsStyle.greyColor2,
    );
  }

  static TextStyle medium16({required BuildContext context}) {
    return TextStyle(
      fontSize: responsiveTextHelper(fontSize: 16, context: context),
      fontWeight: FontWeight.w500,
      fontFamily: FontFamily.inter,
      color: ColorsStyle.blackColor,
    );
  }

  static TextStyle semiBold16({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.poppins,
      fontWeight: FontWeight.w600,
      fontSize: responsiveTextHelper(fontSize: 16, context: context),
      color: ColorsStyle.littleBlackColor,
    );
  }

  static TextStyle bold16({required BuildContext context}) {
    return TextStyle(
      fontSize: responsiveTextHelper(fontSize: 16, context: context),
      fontWeight: FontWeight.w700,
      fontFamily: FontFamily.tajawal,
      color: ColorsStyle.mediumBlackColor2,
    );
  }

  static TextStyle medium18({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.inter,
      fontWeight: FontWeight.w500,
      fontSize: responsiveTextHelper(fontSize: 18, context: context),
      color: ColorsStyle.blackColor,
    );
  }

  static TextStyle bold20({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.inter,
      fontWeight: FontWeight.w700,
      fontSize: responsiveTextHelper(fontSize: 20, context: context),
      color: ColorsStyle.littleBlackColor,
    );
  }

  static TextStyle medium24({required BuildContext context}) {
    return TextStyle(
      fontSize: responsiveTextHelper(fontSize: 24, context: context),
      fontFamily: FontFamily.tajawal,
      fontWeight: FontWeight.w500,
      color: ColorsStyle.littleGreyColor,
    );
  }

  static TextStyle bold24({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.tajawal,
      fontWeight: FontWeight.w700,
      fontSize: responsiveTextHelper(fontSize: 24, context: context),
      color: ColorsStyle.blackColor,
    );
  }

  static TextStyle medium32({required BuildContext context}) {
    return TextStyle(
      fontFamily: FontFamily.poppins,
      fontWeight: FontWeight.w500,
      fontSize: responsiveTextHelper(fontSize: 32, context: context),
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
