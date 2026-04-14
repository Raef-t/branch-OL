import 'package:flutter/material.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_right_side_inside_details_card_in_courses_details_view.dart';
import '/features/courses_details/presentation/view/widgets/custom_two_color_pie_chart_in_courses_details_view.dart';

class CustomContainDetailsCardInCoursesDetailsView extends StatelessWidget {
  const CustomContainDetailsCardInCoursesDetailsView({
    super.key,
    required this.circleColor,
    required this.verticalLineColor,
    required this.batchesModel,
  });
  final Color circleColor, verticalLineColor;
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          CustomTwoColorPieChartInCoursesDetailsView(
            completedModules: batchesModel.attendancePercentage ?? 0,
            color: circleColor,
          ),
          const Spacer(),
          CustomRightSideInsideDetailsCardInCoursesDetailsView(
            verticalLineColor: verticalLineColor,
            batchesModel: batchesModel,
          ),
        ],
      ),
    );
  }
}
