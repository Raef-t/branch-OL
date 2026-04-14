import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_axis_titles_to_left_and_right_and_top_titles_in_details_student_view_helper.dart';
import '/core/helpers/build_bottom_axis_titles_in_details_student_helper.dart';

FlTitlesData buildFlTitlesDataInDetailsStudentViewHelper({
  required BuildContext context,
}) {
  return FlTitlesData(
    leftTitles:
        buildAxisTitlesToLeftAndRightAndTopTitlesInDetailsStudentViewHelper(),
    rightTitles:
        buildAxisTitlesToLeftAndRightAndTopTitlesInDetailsStudentViewHelper(),
    topTitles:
        buildAxisTitlesToLeftAndRightAndTopTitlesInDetailsStudentViewHelper(),
    bottomTitles: buildAxisTitlesToBottomTitlesInDetailsStudentViewHelper(
      context: context,
    ),
  );
}
