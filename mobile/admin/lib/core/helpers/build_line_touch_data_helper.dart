import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_touch_tooltip_data_helper.dart';

LineTouchData buildLineTouchDataHelper({
  required BuildContext context,
  void Function(FlTouchEvent, LineTouchResponse?)? touchCallback,
}) {
  return LineTouchData(
    enabled: true,
    handleBuiltInTouches: false,
    touchCallback: touchCallback,
    touchTooltipData: buildtouchTooltipDataHelper(context: context),
  );
}
