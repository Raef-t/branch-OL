import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/features/details_students/presentation/view/widgets/custom_bar_chart_in_details_student_view.dart';
import '/features/details_students/presentation/view/widgets/custom_header_in_rating_card_in_details_student_view.dart';

class CustomContainRatingCardInDetailsStudentView extends StatelessWidget {
  const CustomContainRatingCardInDetailsStudentView({
    super.key,
    required this.selectedValue,
    required this.maxRating,
    required this.onSelected,
    required this.ratings,
  });
  final String selectedValue;
  final double maxRating;
  final void Function(String) onSelected;
  final List<double> ratings;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Column(
      children: [
        CustomHeaderInRatingCardInDetailsStudentView(
          selectedValue: selectedValue,
          onSelected: onSelected,
        ),
        isRotait
            ? Heights.height9(context: context)
            : Heights.height18(context: context),
        CustomBarChartInDetailsStudentView(
          maxRating: maxRating,
          ratings: ratings,
        ),
      ],
    );
  }
}
