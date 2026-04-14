import 'package:flutter/material.dart';
import '/core/helpers/build_offset_y1_go_to_bottom_and_x0_helper.dart';
import '/core/styles/colors_style.dart';

BoxShadow buildBoxShadowBlur3AndSpread1AndOffsetY1GoToBottomAndX0Helper({
  required BuildContext context,
}) {
  double height = MediaQuery.sizeOf(context).height;
  return BoxShadow(
    color: ColorsStyle.blackColor.withAlpha(40),
    blurRadius: height * 0.004,
    spreadRadius: height * 0.002,
    offset: buildOffsetY1GoToBottomAndX0Helper(context: context),
  );
}
