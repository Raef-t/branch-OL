import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_bar_rods_in_details_student_view_helper.dart';

List<BarChartGroupData> buildBarGroupsInDetailsStudentViewHelper({
  required BuildContext context,
  required double maxRating,
  required List<double> ratings,
}) {
  return List.generate(ratings.length, (index) {
    return BarChartGroupData(
      x: index,
      barRods: buildBarRodsInDetailsStudentViewHelper(
        context: context,
        index: index,
        maxRating: maxRating,
        ratings: ratings[index],
      ),
    );
  });
}
