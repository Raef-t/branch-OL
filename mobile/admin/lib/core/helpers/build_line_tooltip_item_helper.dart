import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';

LineTooltipItem buildLineTooltipItemHelper({
  required LineBarSpot spot,
  required BuildContext context,
}) {
  return LineTooltipItem(
    '${spot.y}%',
    TextsStyle.medium14(
      context: context,
    ).copyWith(color: ColorsStyle.littleVinicColor),
  );
}
