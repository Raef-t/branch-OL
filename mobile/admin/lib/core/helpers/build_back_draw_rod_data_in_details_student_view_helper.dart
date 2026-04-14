import 'package:fl_chart/fl_chart.dart';
import '/core/styles/colors_style.dart';

BackgroundBarChartRodData buildBackDrawRodDataInDetailsStudentViewHelper({
  required double maxRating,
}) {
  return BackgroundBarChartRodData(
    show: true,
    toY: maxRating,
    color: ColorsStyle.veryLittleWhiteColor2,
  );
}
