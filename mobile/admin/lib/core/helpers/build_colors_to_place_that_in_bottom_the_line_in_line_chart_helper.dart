import 'package:fl_chart/fl_chart.dart';
import '/core/helpers/build_linear_gradient_to_line_chart_helper.dart';

BarAreaData buildColorsToPlaceThatInBottomTheLineInLineChartHelper() {
  return BarAreaData(
    show: true,
    gradient: buildLinearGradientToLineChartHelper(),
  );
}
