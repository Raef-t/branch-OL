import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

LinearGradient buildLinearGradientToBigCircleInCoursesViewHelper() {
  return LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      ColorsStyle.veryLittleOrangeColor.withAlpha(50),
      ColorsStyle.littleYellowColor.withAlpha(50),
      ColorsStyle.mediumPinkColor.withAlpha(50),
    ],
  );
}
