import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_bar_groups_in_details_student_view_helper.dart';
import '/core/helpers/build_fl_titles_data_in_details_student_view_helper.dart';

BarChartData buildBarChartDataInDetailsStudentViewHelper({
  required BuildContext context,
  required double maxRating,
  required List<double> ratings,
}) {
  return BarChartData(
    maxY: maxRating,
    gridData: const FlGridData(show: false),
    borderData: FlBorderData(show: false),
    titlesData: buildFlTitlesDataInDetailsStudentViewHelper(context: context),
    barGroups: buildBarGroupsInDetailsStudentViewHelper(
      context: context,
      maxRating: maxRating,
      ratings: ratings,
    ),
  );
}
