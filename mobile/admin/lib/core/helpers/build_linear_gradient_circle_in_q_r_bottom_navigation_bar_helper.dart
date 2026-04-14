import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

LinearGradient buildLinearGradientCircleInQRBottomNavigationBarHelper() {
  return const LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [ColorsStyle.deepRussetColor, ColorsStyle.littleRussetColor],
  );
}
