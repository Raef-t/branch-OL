import 'package:fl_chart/fl_chart.dart';
import '/core/helpers/put_yellow_circle_inside_dot_line_chart_helper.dart';

FlDotData buildDotsInLineChartHelper() {
  return FlDotData(
    show: true,
    getDotPainter: (spot, percent, barData, index) {
      return putYellowCircleInsideDotInLineChartHelper(index: index);
    },
  );
}
