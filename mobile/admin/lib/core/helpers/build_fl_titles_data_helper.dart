import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/axis_titles_to_bottom_titles_helper.dart';
import '/features/home/presentation/managers/models/batch_average/batch_average_model.dart';

FlTitlesData buildFlTitlesDataHelper({
  required BuildContext context,
  required List<BatchAverageModel> listOfBatchAverageModel,
  void Function(String batchName)? onBatchNameTap,
}) {
  return FlTitlesData(
    leftTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
    topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
    rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
    bottomTitles: axisTitlesToBottomTitlesHelper(
      context: context,
      listOfBatchAverageModel: listOfBatchAverageModel,
      onBatchNameTap: onBatchNameTap,
    ),
  );
}
