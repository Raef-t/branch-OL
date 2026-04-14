import 'package:fl_chart/fl_chart.dart';
import '/core/lists/colors_dots_in_line_chart_home_view_list.dart';
import '/core/styles/colors_style.dart';

FlDotCirclePainter putYellowCircleInsideDotInLineChartHelper({
  required int index,
}) {
  return FlDotCirclePainter(
    radius: 2.5,
    color:
        colorsDotsInLineChartHomeViewList[index %
            colorsDotsInLineChartHomeViewList.length],
    strokeWidth: 2,
    strokeColor: ColorsStyle.littleRussetColor,
  );
}
