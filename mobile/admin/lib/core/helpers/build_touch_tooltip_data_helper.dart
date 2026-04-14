import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_line_tooltip_item_helper.dart';
import '/core/styles/colors_style.dart';

LineTouchTooltipData buildtouchTooltipDataHelper({
  required BuildContext context,
}) {
  return LineTouchTooltipData(
    getTooltipColor: (touchedSpot) => ColorsStyle.whiteColor,
    getTooltipItems: (touchedSpots) {
      return touchedSpots.map((spot) {
        return buildLineTooltipItemHelper(context: context, spot: spot);
      }).toList();
    },
  );
}
