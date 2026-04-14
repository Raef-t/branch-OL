import 'package:flutter/material.dart';
import '/core/helpers/get_scale_factor_helper.dart';

double responsiveTextHelper({
  required double fontSize,
  required BuildContext context,
}) {
  double scaleFactor = getScaleFactorHelper(context: context);
  double newFontSize = scaleFactor * fontSize;
  double upperLimit = fontSize * 2.5; // Allow more growth on tablets
  double lowerLimit = fontSize * 0.7; // Allow more shrink on tiny devices
  return newFontSize.clamp(lowerLimit, upperLimit);
}
