import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

LinearGradient
buildLinearGradientToSelectedCardTabBarInCoursesDetailsViewHelper() {
  return const LinearGradient(
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
    colors: [ColorsStyle.littleVinicColor, ColorsStyle.deepPinkColor3],
  );
}
