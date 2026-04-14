import 'package:flutter/material.dart';
import '/core/helpers/build_offset_x0_and_y2_go_to_bottom_helper.dart';
import '/core/styles/colors_style.dart';

BoxShadow buildBoxShadowBlur6AndSpread2AndOffsetX0AndY2GoToBottomHelper({
  required BuildContext context,
}) {
  double height = MediaQuery.sizeOf(context).height;
  return BoxShadow(
    offset: buildOffsetX0AndY2GoToBottomHelper(context: context),
    blurRadius: height * 0.009,
    spreadRadius: height * 0.004,
    color: ColorsStyle.blackColor.withAlpha(50),
  );
}
