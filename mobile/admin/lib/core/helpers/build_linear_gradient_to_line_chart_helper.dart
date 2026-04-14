import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

LinearGradient buildLinearGradientToLineChartHelper() {
  return LinearGradient(
    colors: [
      ColorsStyle.littlePinkColor.withAlpha(50),
      ColorsStyle.deepWhiteColor,
    ],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );
}
