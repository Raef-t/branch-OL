import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_fl_titles_data_helper.dart';
import '/core/helpers/build_full_styles_to_line_chart_helper.dart';
import '/core/helpers/build_line_touch_data_helper.dart';
import '/features/home/presentation/managers/models/batch_average/batch_average_model.dart';

LineChartData buildLineChartDataHelper({
  required BuildContext context,
  required List<BatchAverageModel> listOfBatchAverageModel,
  void Function(FlTouchEvent, LineTouchResponse?)? touchCallback,
  List<ShowingTooltipIndicators> showingTooltipIndicators = const [],
  void Function(String batchName)? onBatchNameTap,
}) {
  return LineChartData(
    gridData: const FlGridData(show: false),
    titlesData: buildFlTitlesDataHelper(
      context: context,
      listOfBatchAverageModel: listOfBatchAverageModel,
      onBatchNameTap: onBatchNameTap,
    ),
    borderData: FlBorderData(show: false),
    minX: 0,
    maxX: (listOfBatchAverageModel.length - 1).toDouble(),
    minY: 0,
    maxY: 100,
    lineBarsData: buildFullStylesToLineChartHelper(
      listOfBatchAverageModel: listOfBatchAverageModel,
    ),
    lineTouchData: buildLineTouchDataHelper(
      context: context,
      touchCallback: touchCallback,
    ),
    showingTooltipIndicators: showingTooltipIndicators,
  );
}
