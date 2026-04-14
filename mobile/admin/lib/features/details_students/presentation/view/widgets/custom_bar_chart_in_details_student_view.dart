import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import '/core/helpers/build_bar_chart_data_in_details_student_view_helper.dart';

class CustomBarChartInDetailsStudentView extends StatelessWidget {
  const CustomBarChartInDetailsStudentView({
    super.key,
    required this.maxRating,
    required this.ratings,
  });
  final double maxRating;
  final List<double> ratings;
  @override
  Widget build(BuildContext context) {
    double height = MediaQuery.sizeOf(context).height;
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return SizedBox(
      height: height * (isRotait ? 0.234 : 0.3),
      child: BarChart(
        buildBarChartDataInDetailsStudentViewHelper(
          context: context,
          maxRating: maxRating,
          ratings: ratings,
        ),
      ),
    );
  }
}
