import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

LinearGradient buildLinearGradientToSaveButtonInFilterExamsView2Helper() {
  return const LinearGradient(
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
    colors: [ColorsStyle.deepRussetColor, ColorsStyle.deepPinkColor4],
  );
}
