import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_get_titles_widget_in_details_student_helper.dart';

SideTitles buildSideTitlesInDetailsStudentHelper({
  required BuildContext context,
}) {
  return SideTitles(
    showTitles: true,
    getTitlesWidget: buildgetTitlesWidgetInDetailsStudentHelper(
      context: context,
    ),
  );
}
