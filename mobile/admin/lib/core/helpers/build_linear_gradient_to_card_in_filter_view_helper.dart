import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

LinearGradient buildLinearGradientToCardInFilterViewHelper() {
  return const LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [ColorsStyle.littleVinicColor, ColorsStyle.mediumRussetColor3],
  );
}
