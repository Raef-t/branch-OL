import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_side_titles_in_details_student_helper.dart';

AxisTitles buildAxisTitlesToBottomTitlesInDetailsStudentViewHelper({
  required BuildContext context,
}) {
  return AxisTitles(
    sideTitles: buildSideTitlesInDetailsStudentHelper(context: context),
  );
}
