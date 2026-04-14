import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';
import '/features/home/presentation/managers/models/batch_average/batch_average_model.dart';

AxisTitles axisTitlesToBottomTitlesHelper({
  required BuildContext context,
  required List<BatchAverageModel> listOfBatchAverageModel,
  void Function(String batchName)? onBatchNameTap,
}) {
  return AxisTitles(
    sideTitles: SideTitles(
      showTitles: true,
      interval: 1,
      reservedSize: 60,
      getTitlesWidget: (value, meta) {
        final index = value.toInt();
        if (index < 0 || index >= listOfBatchAverageModel.length) {
          return const SizedBox();
        }
        final fullText =
            listOfBatchAverageModel[index].batchName ?? 'لا يوجد شعبة';
        final displayText = (fullText.length > 8)
            ? '${fullText.substring(0, 8)}..'
            : fullText;
        return GestureDetector(
          onTap: () => onBatchNameTap?.call(fullText),
          child: RotatedBox(
            quarterTurns: 1,
            child: Text(
              displayText,
              style: TextsStyle.normal13(context: context),
            ),
          ),
        );
      },
    ),
  );
}
