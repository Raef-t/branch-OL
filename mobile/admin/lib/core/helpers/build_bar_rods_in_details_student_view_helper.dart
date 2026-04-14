import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/helpers/build_back_draw_rod_data_in_details_student_view_helper.dart';
import '/core/styles/colors_style.dart';

List<BarChartRodData> buildBarRodsInDetailsStudentViewHelper({
  required BuildContext context,
  required int index,
  required double maxRating,
  required double? ratings,
}) {
  double width = MediaQuery.sizeOf(context).width;
  return [
    BarChartRodData(
      toY: ratings ?? 0,
      width: width * 0.047,
      borderRadius: Circulars.circular6(context: context),
      color: ColorsStyle.littleVinicColor,
      backDrawRodData: buildBackDrawRodDataInDetailsStudentViewHelper(
        maxRating: maxRating,
      ),
    ),
  ];
}
