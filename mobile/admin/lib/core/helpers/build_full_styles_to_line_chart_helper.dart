import 'package:fl_chart/fl_chart.dart';
import '/core/helpers/build_colors_to_place_that_in_bottom_the_line_in_line_chart_helper.dart';
import '/core/helpers/build_dots_in_line_chart_helper.dart';
import '/core/styles/colors_style.dart';
import '/features/home/presentation/managers/models/batch_average/batch_average_model.dart';

List<LineChartBarData> buildFullStylesToLineChartHelper({
  required List<BatchAverageModel> listOfBatchAverageModel,
}) {
  return [
    LineChartBarData(
      spots: List.generate(
        listOfBatchAverageModel.length,
        (index) => FlSpot(
          index.toDouble(),
          listOfBatchAverageModel[index].rating ?? 0,
        ),
      ),
      isCurved: true,
      color: ColorsStyle.littleRussetColor,
      barWidth: 2,
      isStrokeCapRound: true,
      dotData: buildDotsInLineChartHelper(),
      belowBarData: buildColorsToPlaceThatInBottomTheLineInLineChartHelper(),
    ),
  ];
}
