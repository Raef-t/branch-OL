import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

LinearGradient buildLinearGradientToRegisterCardButtonInAuthViewHelper() {
  return const LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [ColorsStyle.littleVinicColor, ColorsStyle.mediumRussetColor3],
  );
}
